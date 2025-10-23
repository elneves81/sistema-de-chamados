<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Admin\UbsDashboardController;
use App\Http\Controllers\LdapImportController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\UserMessageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // Se estiver autenticado, vai para o dashboard (mas só se não estiver vindo de logout)
    if (auth()->check()) {
        // Verificar se não está vindo de logout
        if (!session()->has('logout_redirect')) {
            return redirect()->route('dashboard');
        }
        // Se estiver vindo de logout, limpa a flag e mostra login
        session()->forget('logout_redirect');
        auth()->logout();
    }
    // Se não estiver autenticado, mostra página de status/login
    return view('system-status');
});

Auth::routes();

// Rota GET de logout segura (para casos onde o JavaScript falha)
Route::get('/logout', function () {
    session()->put('logout_redirect', true);
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/')->with('message', 'Logout realizado com sucesso!');
})->name('logout.get')->middleware('auth');

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TicketController::class, 'dashboard'])->name('dashboard');
        Route::get('/home', [\App\Http\Controllers\TicketController::class, 'dashboard'])->name('home');
    });
    
    // Tickets - verificações de permissão dentro do controller
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.comments.store');
    Route::post('tickets/bulk-action', [TicketController::class, 'bulkAction'])->name('tickets.bulk-action');
    
    // Categorias - ROTAS CORRIGIDAS (ordem e restrições)
    Route::middleware('permission:categories.view')->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    });
    
    Route::middleware('permission:categories.manage')->group(function () {
        // Rotas específicas PRIMEIRO (antes do parâmetro dinâmico)
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        
        // Rotas com parâmetros - restringir para números apenas
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit')->whereNumber('category');
        Route::put('categories/{category}', [CategoryController::class, 'update'])
            ->name('categories.update')->whereNumber('category');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy')->whereNumber('category');
    });
    
    // Rota show por último e restrita a números
    Route::middleware('permission:categories.view')->group(function () {
        Route::get('categories/{category}', [CategoryController::class, 'show'])
            ->name('categories.show')->whereNumber('category');
    });
    
    // Rotas administrativas
    Route::prefix('admin')->name('admin.')->group(function () {
        // Usuários
        Route::middleware('permission:users.view')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
            Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        });
        
        Route::middleware('permission:users.create')->group(function () {
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
        });
        
        Route::middleware('permission:users.edit')->group(function () {
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        });
        
        Route::middleware('permission:users.delete')->group(function () {
            Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
        
        // Importação de usuários
        Route::middleware('permission:users.create')->group(function () {
            Route::post('users/import', [UserController::class, 'import'])->name('users.import');
            Route::get('users/export', [UserController::class, 'export'])->name('users.export');
            Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
            Route::post('users/{user}/assign-location', [UserController::class, 'assignLocation'])->name('users.assign-location');
        });
        
        // Gerenciamento de permissões (apenas super admin)
        Route::middleware('permission:users.permissions')->group(function () {
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
            Route::get('permissions/{user}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
            Route::put('permissions/{user}', [PermissionController::class, 'update'])->name('permissions.update');
            Route::post('permissions/{user}/apply-default', [PermissionController::class, 'applyDefaultPermissions'])->name('permissions.apply-default');
        });
        
        // Monitoramento do sistema
        Route::middleware('permission:system.monitoring')->group(function () {
            Route::get('monitoring', [DashboardController::class, 'monitoring'])->name('monitoring');
            Route::get('api/tickets/realtime', [DashboardController::class, 'realtimeTickets'])->name('api.tickets.realtime');
        });
        
        // Importação LDAP
        Route::group([], function () {
            Route::get('/ldap/import', [\App\Http\Controllers\LdapImportController::class, 'showImportForm'])->name('ldap.import.form');
            Route::post('/ldap/preview', [\App\Http\Controllers\LdapImportController::class, 'preview'])->name('ldap.import.preview');
            Route::get('/ldap/preview', function() {
                return redirect()->route('admin.ldap.import.form')->with('warning', 'Use o formulário para fazer preview dos usuários LDAP.');
            });
            Route::post('/ldap/import', [\App\Http\Controllers\LdapImportController::class, 'import'])->name('ldap.import.process');
            Route::post('/ldap/test-connection', [\App\Http\Controllers\LdapImportController::class, 'testConnection'])->name('ldap.test-connection');
            
            // Importação em lotes
            Route::post('/ldap/bulk-import', [\App\Http\Controllers\LdapImportController::class, 'bulkImport'])->name('ldap.import.bulk');
            Route::get('/ldap/progress/{jobId}', [\App\Http\Controllers\LdapImportController::class, 'checkProgress'])->name('ldap.import.progress');
            Route::post('/ldap/cancel/{jobId}', [\App\Http\Controllers\LdapImportController::class, 'cancelImport'])->name('ldap.import.cancel');
            
            // Rota de debug temporária
            Route::post('/ldap/debug', function(\Illuminate\Http\Request $request) {
                \Illuminate\Support\Facades\Log::info('LDAP Debug - Dados recebidos:', $request->all());
                return response()->json([
                    'success' => true,
                    'data' => $request->all(),
                    'users_count' => count($request->input('users', []))
                ]);
            })->name('ldap.debug');
        });

        // Gerenciamento de Localizações
        Route::middleware('permission:users.view')->group(function () {
            Route::resource('locations', \App\Http\Controllers\LocationController::class);
        });

        // Dashboard UBS
        Route::middleware('permission:users.view')->group(function () {
            Route::get('ubs-dashboard', [\App\Http\Controllers\Admin\UbsDashboardController::class, 'index'])->name('ubs.index');
            Route::get('ubs-dashboard/{location}', [\App\Http\Controllers\Admin\UbsDashboardController::class, 'show'])->name('ubs.show');
            Route::get('api/ubs-stats', [\App\Http\Controllers\Admin\UbsDashboardController::class, 'getStats'])->name('ubs.api.stats');
            Route::get('api/ubs-chart-data', [\App\Http\Controllers\Admin\UbsDashboardController::class, 'getChartData'])->name('ubs.api.charts');
        });

        // Sincronização LDAP Automática
        Route::middleware('permission:users.create')->group(function () {
            Route::post('ldap/sync', [\App\Http\Controllers\LdapImportController::class, 'sync'])->name('ldap.sync');
            Route::get('ldap/sync-status', [\App\Http\Controllers\LdapImportController::class, 'syncStatus'])->name('ldap.sync.status');
            Route::post('ldap/dry-run', [\App\Http\Controllers\LdapImportController::class, 'dryRun'])->name('ldap.sync.dry-run');
        });
    });
});

// Sistema IA - Inteligência Artificial (fora do grupo admin)
Route::middleware(['auth', 'permission:dashboard.view'])->group(function () {
    Route::get('ai/dashboard', [AiController::class, 'dashboard'])->name('ai.dashboard');
});

// Rotas de Chat IA (autenticadas)
Route::middleware('auth')->group(function () {
    Route::post('ai/chatbot', [AiController::class, 'chatbot'])->name('ai.chatbot');
    Route::post('ai/classify', [AiController::class, 'classifyTicket'])->name('ai.classify');
    Route::post('ai/create-ticket', [AiController::class, 'createTicketViaAi'])->name('ai.create-ticket');
    Route::post('ai/suggest', [AiController::class, 'autoSuggest'])->name('ai.suggest');
    Route::get('ai/predict', [AiController::class, 'predictDemand'])->name('ai.predict');
    Route::get('ai/dashboard-data', [AiController::class, 'getDashboardData'])->name('ai.dashboard.data');
});

// Fale Conosco - Disponível para todos os usuários autenticados
Route::middleware('auth')->group(function () {
    Route::get('/fale-conosco', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact.index');
    Route::post('/fale-conosco/enviar', [\App\Http\Controllers\ContactController::class, 'sendMessage'])->name('contact.send');
});

// Gerenciamento de mensagens de contato (apenas admins)
Route::middleware(['auth', 'permission:users.view'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('contact-messages', [\App\Http\Controllers\ContactController::class, 'listMessages'])->name('contact.list');
    Route::get('contact-messages/{contactMessage}', [\App\Http\Controllers\ContactController::class, 'showMessage'])->name('contact.show');
    Route::patch('contact-messages/{contactMessage}/status', [\App\Http\Controllers\ContactController::class, 'updateStatus'])->name('contact.update-status');
});

// Sistema de Mensagens Internas
Route::middleware('auth')->group(function () {
    Route::get('/messages', [\App\Http\Controllers\UserMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/compose/{user?}', [\App\Http\Controllers\UserMessageController::class, 'compose'])->name('messages.compose');
    Route::get('/messages/{message}', [\App\Http\Controllers\UserMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{messageId}/reply', [\App\Http\Controllers\UserMessageController::class, 'reply'])->name('messages.reply');
    Route::patch('/messages/{message}/read', [\App\Http\Controllers\UserMessageController::class, 'markAsRead'])->name('messages.read');
    Route::post('/messages/send', [\App\Http\Controllers\UserMessageController::class, 'store'])->name('messages.send');
});

// AJAX Endpoints para notificações - temporariamente simplificado
Route::middleware(['auth'])->group(function () {
    Route::get('/ajax/messages/recent', [\App\Http\Controllers\UserMessageController::class, 'recent'])->name('ajax.messages.recent');
    Route::get('/ajax/messages/unread-count', [\App\Http\Controllers\UserMessageController::class, 'unreadCount'])->name('ajax.messages.unread-count');
    Route::get('/ajax/messages/users', [\App\Http\Controllers\UserMessageController::class, 'getUsersForMessage'])->name('ajax.messages.users');
});

// Painel TV - Acesso público para exibição em TVs (NOVO PAINEL REPAGINADO)
Route::redirect('/painel-tv', '/painel-tv-repaginado');
Route::redirect('/painel-tv-smart', '/painel-tv-repaginado'); // Redirecionar versão antiga
Route::get('/painel-tv-repaginado', [TicketController::class, 'boardTvRepaginado'])->name('tickets.boardTvRepaginado');

// Versões antigas (mantidas para compatibilidade)
Route::get('/painel-tv-enhanced', [TicketController::class, 'boardTvEnhanced'])->name('tickets.boardTvEnhanced');
Route::get('/painel-tv-legacy', [TicketController::class, 'boardTvSmart'])->name('tickets.boardTvLegacy');

// Página de Status do Sistema
Route::get('/system-status', function() {
    return view('system-status');
})->name('system.status');

// Redirect root to dashboard for authenticated users
Route::redirect('/home', '/dashboard');
