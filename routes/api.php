<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas de API para painel TV e dashboard (protegidas)
Route::middleware('auth:sanctum')->group(function () {
    // Painel TV
    Route::get('/tickets/all', [\App\Http\Controllers\TicketController::class, 'apiAll'])->name('api.tickets.all');
    Route::get('/tickets/new', [\App\Http\Controllers\TicketController::class, 'apiNew'])->name('api.tickets.new');
    Route::post('/tickets/update-status', [\App\Http\Controllers\TicketController::class, 'apiUpdateStatus'])->name('api.tickets.updateStatus');

    // Métricas e dashboard
    Route::get('/tickets/metrics', [\App\Http\Controllers\TicketController::class, 'apiMetrics'])->name('api.tickets.metrics');
    Route::get('/tickets/metrics/export', [\App\Http\Controllers\TicketController::class, 'apiExportMetrics'])->name('api.tickets.metrics.export');
    Route::get('/tickets/dashboard/export', [\App\Http\Controllers\TicketController::class, 'apiExportDashboard'])->name('api.tickets.dashboard.export');
});

// ROTA PÚBLICA para painel TV/dashboard (sem autenticação)
Route::get('/tickets/dashboard', [\App\Http\Controllers\TicketController::class, 'apiDashboard'])->name('api.tickets.dashboard');
