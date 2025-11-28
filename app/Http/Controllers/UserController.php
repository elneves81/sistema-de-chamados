<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->except(['search']);
        $this->middleware('auth')->only(['search']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('location');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('deleted_at');
            } else {
                $query->onlyTrashed();
            }
        }

        if ($request->filled('location')) {
            if ($request->location === 'null') {
                $query->whereNull('location_id');
            } else {
                $query->where('location_id', $request->location);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Buscar todas as localizações para os filtros e modal
        $locations = \App\Models\Location::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,technician,customer',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Gerar username a partir do email (parte antes do @)
        $username = explode('@', $request->email)[0];
        
        // Garantir que o username seja único
        $originalUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }

        User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'department' => $request->department,
        ]);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['tickets', 'assignedTickets']);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => ['required','string','max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,technician,customer',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'department' => $request->department,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')
                        ->with('success', 'Usuário removido com sucesso!');
    }

    /**
     * Import users from CSV file
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        try {
            $file = $request->file('file');
            $data = array_map('str_getcsv', file($file->getPathname()));
            $header = array_shift($data);
            
            $importedCount = 0;
            $errors = [];
            
            foreach ($data as $row) {
                $userData = array_combine($header, $row);
                
                // Validar dados
                $validator = Validator::make($userData, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'role' => 'required|in:admin,technician,customer',
                    'password' => 'nullable|string|min:8'
                ]);
                
                if ($validator->fails()) {
                    $errors[] = "Linha " . ($importedCount + 2) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password'] ?? 'password123'),
                    'role' => $userData['role'],
                    'phone' => $userData['phone'] ?? null,
                    'department' => $userData['department'] ?? null,
                ]);
                
                $importedCount++;
            }
            
            $message = "Usuários importados: {$importedCount}";
            if (!empty($errors)) {
                $message .= ". Erros: " . implode('; ', $errors);
            }
            
            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao importar usuários: ' . $e->getMessage());
        }
    }

    /**
     * Export users to CSV
     */
    public function export()
    {
        $users = User::all();
        
        $filename = 'usuarios_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['name', 'email', 'role', 'phone', 'department', 'created_at']);
            
            // Data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->phone,
                    $user->department,
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export a single user's report to PDF
     */
    public function exportPdf(User $user)
    {
        // Carregar relacionamentos e métricas
        $user->load(['location']);

        $stats = [
            'created_total' => $user->tickets()->count(),
            'assigned_total' => $user->assignedTickets()->count(),
            'in_progress' => $user->assignedTickets()->whereIn('status', ['open', 'in_progress'])->count(),
            'resolved' => $user->assignedTickets()->where('status', 'resolved')->count(),
        ];

        $recentCreated = $user->tickets()->with('category')->latest()->limit(10)->get();
        $recentAssigned = $user->assignedTickets()->with(['category','user'])->latest()->limit(10)->get();

        $pdf = Pdf::loadView('admin.users.pdf', [
            'user' => $user,
            'stats' => $stats,
            'recentCreated' => $recentCreated,
            'recentAssigned' => $recentAssigned,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'relatorio_usuario_' . str_replace(' ', '_', strtolower($user->name)) . '_' . now()->format('Ymd_His') . '.pdf';
        return $pdf->download($filename);
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,change_role',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'role' => 'required_if:action,change_role|in:admin,technician,customer'
        ]);

        $users = User::whereIn('id', $request->user_ids)->get();

        switch ($request->action) {
            case 'delete':
                foreach ($users as $user) {
                    $user->delete();
                }
                $message = count($users) . ' usuários removidos com sucesso!';
                break;

            case 'change_role':
                User::whereIn('id', $request->user_ids)->update(['role' => $request->role]);
                $message = count($users) . ' usuários atualizados com sucesso!';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Assign location to user
     */
    public function assignLocation(Request $request, User $user)
    {
        $request->validate([
            'location_id' => 'required|exists:locations,id'
        ]);

        $user->update(['location_id' => $request->location_id]);

        $location = \App\Models\Location::find($request->location_id);
        
        return back()->with('success', "Usuário {$user->name} foi vinculado à {$location->name}!");
    }

    /**
     * Buscar usuários (AJAX) - para autocomplete
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get(['id', 'name', 'email', 'role']);

        return response()->json($users);
    }
}

