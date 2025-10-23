<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserMessageController;

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

// Sistema de Mensagens API (usando autenticação web para funcionar com sessões)
Route::middleware(['auth', 'web'])->group(function () {
    Route::get('/messages/recent', [\App\Http\Controllers\UserMessageController::class, 'recent'])->name('api.messages.recent');
    Route::get('/messages/unread-count', [\App\Http\Controllers\UserMessageController::class, 'unreadCount'])->name('api.messages.unread-count');
});

// ROTA PÚBLICA para painel TV/dashboard (sem autenticação)
Route::get('/tickets/dashboard', [\App\Http\Controllers\TicketController::class, 'apiDashboard'])->name('api.tickets.dashboard');
Route::get('/tickets/realtime', [\App\Http\Controllers\DashboardController::class, 'realtimeTickets'])->name('api.tickets.realtime');

// Rotas de IA - Inteligência Artificial (públicas para facilitar uso)
Route::post('/ai/chatbot', [\App\Http\Controllers\AiController::class, 'chatbot'])->name('api.ai.chatbot');
Route::post('/ai/classify', [\App\Http\Controllers\AiController::class, 'classifyTicket'])->name('api.ai.classify');
Route::post('/ai/create-ticket', [\App\Http\Controllers\AiController::class, 'createTicketViaAi'])->name('api.ai.create-ticket');
Route::get('/ai/predict', [\App\Http\Controllers\AiController::class, 'predictDemand'])->name('api.ai.predict');
Route::get('/ai/dashboard', [\App\Http\Controllers\AiController::class, 'dashboard'])->name('api.ai.dashboard');
Route::post('/ai/suggest', [\App\Http\Controllers\AiController::class, 'autoSuggest'])->name('api.ai.suggest');

// Nova funcionalidade: Criação de chamado via IA (requer autenticação)
Route::middleware('auth')->group(function () {
    Route::post('/ai/create-ticket', [\App\Http\Controllers\AiController::class, 'createTicketViaAi'])->name('api.ai.create-ticket');
});
