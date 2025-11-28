<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetModel;
use App\Models\AssetStatus;
use App\Models\Manufacturer;
use App\Models\Location;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,technician')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['assetModel.manufacturer', 'location', 'assignedUser', 'status', 'department']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('model_id')) {
            $query->where('model_id', $request->model_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Ordenação
        $query->orderBy('created_at', 'desc');

        $assets = $query->paginate(15);

        // Estatísticas usando relacionamentos
        $totalAssets = Asset::count();
        $activeAssets = Asset::whereHas('status', function($q) {
            $q->where('name', 'Em Uso');
        })->count();
        $stockAssets = Asset::whereHas('status', function($q) {
            $q->where('name', 'Em Estoque');
        })->count();
        $maintenanceAssets = Asset::whereHas('status', function($q) {
            $q->where('name', 'Em Manutenção');
        })->count();

        // Para os filtros
        $assetModels = AssetModel::with('manufacturer')->get();
        $manufacturers = Manufacturer::all();
        $locations = Location::orderBy('name', 'asc')->get();
        $departments = Department::all();
        $users = User::where('role', '!=', 'customer')->get();
        $statuses = AssetStatus::where('is_active', true)->get();

        return view('assets.index', compact(
            'assets', 
            'assetModels', 
            'manufacturers', 
            'locations', 
            'departments',
            'users',
            'statuses',
            'totalAssets',
            'activeAssets',
            'stockAssets',
            'maintenanceAssets'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $assetModels = AssetModel::with('manufacturer')->get();
        $manufacturers = Manufacturer::all();
        $locations = Location::orderBy('name', 'asc')->get();
        $departments = Department::all();
        $users = User::where('role', '!=', 'customer')->get();
        $statuses = AssetStatus::where('is_active', true)->get();

        // Se estiver duplicando um ativo
        $duplicateAsset = null;
        if ($request->filled('duplicate')) {
            $duplicateAsset = Asset::find($request->duplicate);
        }

        return view('assets.create', compact('assetModels', 'manufacturers', 'locations', 'departments', 'users', 'statuses', 'duplicateAsset'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'asset_tag' => 'required|string|max:255|unique:assets',
            'serial_number' => 'nullable|string|max:255',
            'model_id' => 'required|exists:asset_models,id',
            'status_id' => 'required|exists:asset_statuses,id',
            'location_id' => 'nullable|exists:locations,id',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_expires' => 'nullable|date',
            'notes' => 'nullable|string',
            'custom_fields' => 'nullable|array'
        ]);

        Asset::create($validated);

        return redirect()->route('assets.index')
            ->with('success', 'Ativo criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        $asset->load(['assetModel.manufacturer', 'location', 'department', 'status', 'assignedUser', 'tickets']);
        
        return view('assets.show', compact('asset'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $assetModels = AssetModel::with('manufacturer')->get();
        $manufacturers = Manufacturer::all();
        $locations = Location::orderBy('name', 'asc')->get();
        $departments = Department::all();
        $users = User::where('role', '!=', 'customer')->get();
        $statuses = AssetStatus::where('is_active', true)->get();

        return view('assets.edit', compact('asset', 'assetModels', 'manufacturers', 'locations', 'departments', 'users', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'asset_tag' => 'required|string|max:255|unique:assets,asset_tag,' . $asset->id,
            'serial_number' => 'nullable|string|max:255',
            'model_id' => 'required|exists:asset_models,id',
            'status_id' => 'required|exists:asset_statuses,id',
            'location_id' => 'nullable|exists:locations,id',
            'department_id' => 'nullable|exists:departments,id',
            'assigned_to' => 'nullable|exists:users,id',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'warranty_expires' => 'nullable|date',
            'notes' => 'nullable|string',
            'custom_fields' => 'nullable|array'
        ]);

        $asset->update($validated);

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Ativo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        if ($asset->tickets()->count() > 0) {        return redirect()->route('assets.index')
            ->with('error', 'Não é possível excluir este ativo pois há tickets vinculados.');
        }

        // Remover imagem se existir
        if ($asset->image) {
            Storage::disk('public')->delete($asset->image);
        }

        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Ativo excluído com sucesso!');
    }

    /**
     * Export assets to CSV
     */
    public function export(Request $request)
    {
        $query = Asset::with(['assetType', 'manufacturer', 'location', 'assignedUser']);

        // Aplicar os mesmos filtros da listagem
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('asset_tag', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('asset_type_id')) {
            $query->where('asset_type_id', $request->asset_type_id);
        }

        if ($request->filled('manufacturer_id')) {
            $query->where('manufacturer_id', $request->manufacturer_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $assets = $query->orderBy('created_at', 'desc')->get();

        $filename = 'assets_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($assets) {
            $file = fopen('php://output', 'w');

            // Cabeçalhos
            fputcsv($file, [
                'Patrimônio',
                'Nome',
                'Tipo',
                'Fabricante',
                'Modelo',
                'Número de Série',
                'Status',
                'Localização',
                'Atribuído a',
                'E-mail do Usuário',
                'Data de Compra',
                'Valor de Compra',
                'Garantia até',
                'Especificações',
                'Observações',
                'Criado em',
                'Atualizado em'
            ]);

            // Dados
            foreach ($assets as $asset) {
                fputcsv($file, [
                    $asset->asset_tag,
                    $asset->name,
                    $asset->assetType ? $asset->assetType->name : '',
                    $asset->manufacturer ? $asset->manufacturer->name : '',
                    $asset->model ?? '',
                    $asset->serial_number ?? '',
                    ucfirst($asset->status),
                    $asset->location ? $asset->location->name : '',
                    $asset->assignedUser ? $asset->assignedUser->name : '',
                    $asset->assignedUser ? $asset->assignedUser->email : '',
                    $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : '',
                    $asset->purchase_cost ? 'R$ ' . number_format($asset->purchase_cost, 2, ',', '.') : '',
                    $asset->warranty_end ? $asset->warranty_end->format('d/m/Y') : '',
                    $asset->specifications ?? '',
                    $asset->notes ?? '',
                    $asset->created_at->format('d/m/Y H:i'),
                    $asset->updated_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
