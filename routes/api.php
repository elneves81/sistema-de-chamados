<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobileTicketController;
use App\Http\Controllers\Api\MobileDeviceController;
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

// ===== Mobile Auth (Sanctum tokens) =====
// Rate limit login to mitigate brute force (15 req/min per IP or user key)
Route::middleware('throttle:15,1')->post('/auth/login', [MobileAuthController::class, 'login']);
Route::middleware(['auth:sanctum','throttle:60,1'])->post('/auth/logout', [MobileAuthController::class, 'logout']);

// ===== Mobile Tickets (Tecnicos) =====
// Protect with Sanctum and apply per-token rate limiting (60 req/min)
Route::prefix('mobile')->middleware(['auth:sanctum','throttle:60,1'])->group(function () {
    Route::get('/tickets', [MobileTicketController::class, 'index']);
    Route::get('/tickets/{ticket}', [MobileTicketController::class, 'show']);
    Route::put('/tickets/{ticket}/status', [MobileTicketController::class, 'updateStatus']);
    Route::post('/tickets/{ticket}/comment', [MobileTicketController::class, 'addComment']);
    Route::post('/tickets/{ticket}/claim', [MobileTicketController::class, 'claim']);

    // Registro de device para push notifications
    Route::post('/devices/register', [MobileDeviceController::class, 'register']);
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

// Rotas de IA - Inteligência Artificial (públicas para facilitar uso) com rate limit
Route::middleware('throttle:30,1')->group(function () {
    Route::post('/ai/chatbot', [\App\Http\Controllers\AiController::class, 'chatbot'])->name('api.ai.chatbot');
    Route::post('/ai/classify', [\App\Http\Controllers\AiController::class, 'classifyTicket'])->name('api.ai.classify');
    Route::post('/ai/create-ticket', [\App\Http\Controllers\AiController::class, 'createTicketViaAi'])->name('api.ai.create-ticket');
    Route::get('/ai/predict', [\App\Http\Controllers\AiController::class, 'predictDemand'])->name('api.ai.predict');
    Route::get('/ai/dashboard', [\App\Http\Controllers\AiController::class, 'dashboard'])->name('api.ai.dashboard');
    Route::post('/ai/suggest', [\App\Http\Controllers\AiController::class, 'autoSuggest'])->name('api.ai.suggest');
});

// Nova funcionalidade: Criação de chamado via IA (requer autenticação)
Route::middleware('auth')->group(function () {
    Route::post('/ai/create-ticket', [\App\Http\Controllers\AiController::class, 'createTicketViaAi'])->name('api.ai.create-ticket');
});
