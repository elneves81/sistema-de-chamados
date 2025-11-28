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
    // Se estiver autenticado, vai para o dashboard
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    // Não autenticado: redireciona diretamente para /login (mantém host/porta atuais)
    return redirect('/login');
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
    // Perfil do Usuário
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // Dashboard
    Route::middleware('permission:dashboard.view')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TicketController::class, 'dashboard'])->name('dashboard');
        Route::get('/home', [\App\Http\Controllers\TicketController::class, 'dashboard'])->name('home');

        // Exportações do Dashboard (autenticadas) – usam as mesmas actions da API, porém com sessão web
        Route::middleware('permission:dashboard.export')->group(function () {
            Route::get('/dashboard/export/preview', [\App\Http\Controllers\TicketController::class, 'dashboardExportPreview'])
                ->name('dashboard.export.preview');
            Route::get('/dashboard/export', [\App\Http\Controllers\TicketController::class, 'apiExportDashboard'])
                ->name('dashboard.export');
            Route::get('/dashboard/metrics/export', [\App\Http\Controllers\TicketController::class, 'apiExportMetrics'])
                ->name('dashboard.metrics.export');
        });
    });
    
    // Dashboard Kanban para Técnicos
    Route::prefix('technician')->name('technician.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\TechnicianDashboardController::class, 'index'])->name('dashboard');
        Route::get('refresh', [\App\Http\Controllers\TechnicianDashboardController::class, 'refresh'])->name('refresh');
        Route::post('assign', [\App\Http\Controllers\TechnicianDashboardController::class, 'assignTicket'])->name('assign');
        Route::post('unassign', [\App\Http\Controllers\TechnicianDashboardController::class, 'unassignTicket'])->name('unassign');
        Route::post('update-status', [\App\Http\Controllers\TechnicianDashboardController::class, 'updateStatus'])->name('update-status');
    });
    
    // Tickets - verificações de permissão dentro do controller
    Route::resource('tickets', TicketController::class);
    Route::post('tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.comments.store');
    Route::post('tickets/bulk-action', [TicketController::class, 'bulkAction'])->name('tickets.bulk-action');
    
    // Atendimento Colaborativo - Técnico de Suporte
    Route::post('tickets/{ticket}/support-technician', [TicketController::class, 'assignSupportTechnician'])
        ->name('tickets.support.assign');
    Route::delete('tickets/{ticket}/support-technician', [TicketController::class, 'removeSupportTechnician'])
        ->name('tickets.support.remove');
    
    // Máquinas / Inventário
    // IMPORTANTE: Rotas específicas (create, edit) VÊM ANTES das rotas com parâmetros dinâmicos
    
    // Rota de listagem
    Route::get('machines', [\App\Http\Controllers\MachineController::class, 'index'])
        ->name('machines.index')
        ->middleware('permission:machines.view');
    
    // Rotas de criação (ANTES de {machine})
    Route::get('machines/create', [\App\Http\Controllers\MachineController::class, 'create'])
        ->name('machines.create')
        ->middleware('permission:machines.create');
    
    Route::get('machines/create-tablet', [\App\Http\Controllers\MachineController::class, 'createTablet'])
        ->name('machines.create.tablet')
        ->middleware('permission:machines.create');
    
    Route::post('machines', [\App\Http\Controllers\MachineController::class, 'store'])
        ->name('machines.store')
        ->middleware('permission:machines.create');
    
    // Validação de assinatura via LDAP (qualquer usuário autenticado pode validar)
    Route::post('machines/validate-signature', [\App\Http\Controllers\MachineController::class, 'validateSignature'])
        ->name('machines.validate-signature');
    
    // Salvar assinatura digital
    Route::post('machines/{machine}/save-signature', [\App\Http\Controllers\MachineController::class, 'saveSignature'])
        ->name('machines.save-signature');
    
    // Atualizar status da assinatura validada (qualquer usuário autenticado pode validar)
    Route::post('machines/{machine}/validate-signature-status', [\App\Http\Controllers\MachineController::class, 'updateSignatureStatus'])
        ->name('machines.validate-signature-status');
    
    // Buscar usuários para validação
    Route::get('machines/search-users', [\App\Http\Controllers\MachineController::class, 'searchUsers'])
        ->name('machines.search-users');
    
    // Rota para servir assinatura como imagem
    Route::get('machines/{machine}/signature', [\App\Http\Controllers\MachineController::class, 'getSignature'])
        ->name('machines.signature');
    
    // Rotas de edição (ANTES de {machine})
    Route::get('machines/{machine}/edit', [\App\Http\Controllers\MachineController::class, 'edit'])
        ->name('machines.edit')
        ->middleware('permission:machines.edit');
    
    Route::put('machines/{machine}', [\App\Http\Controllers\MachineController::class, 'update'])
        ->name('machines.update')
        ->middleware('permission:machines.edit');
    
    // Rota de exclusão
    Route::delete('machines/{machine}', [\App\Http\Controllers\MachineController::class, 'destroy'])
        ->name('machines.destroy')
        ->middleware('permission:machines.delete');
    
    // Rota de visualização individual (POR ÚLTIMO, pois tem parâmetro dinâmico)
    Route::get('machines/{machine}', [\App\Http\Controllers\MachineController::class, 'show'])
        ->name('machines.show')
        ->middleware('permission:machines.view');
    
    // Ramais - Todos autenticados podem visualizar, apenas admin e técnicos podem editar
    Route::get('ramais', [\App\Http\Controllers\RamalController::class, 'index'])
        ->name('ramais.index');
    
    Route::get('ramais/create', [\App\Http\Controllers\RamalController::class, 'create'])
        ->name('ramais.create');
    
    Route::post('ramais', [\App\Http\Controllers\RamalController::class, 'store'])
        ->name('ramais.store');
    
    Route::get('ramais/{ramal}/edit', [\App\Http\Controllers\RamalController::class, 'edit'])
        ->name('ramais.edit');
    
    Route::put('ramais/{ramal}', [\App\Http\Controllers\RamalController::class, 'update'])
        ->name('ramais.update');
    
    Route::delete('ramais/{ramal}', [\App\Http\Controllers\RamalController::class, 'destroy'])
        ->name('ramais.destroy');
    
    Route::get('ramais/{ramal}', [\App\Http\Controllers\RamalController::class, 'show'])
        ->name('ramais.show');
    
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
        // Usuários - ordem correta: rotas específicas ANTES das dinâmicas
        // Busca AJAX acessível para qualquer usuário autenticado (sem exigir permission:users.view)
        Route::get('users/search', [UserController::class, 'search'])->name('users.search');

        Route::middleware('permission:users.view')->group(function () {
            Route::get('users', [UserController::class, 'index'])->name('users.index');
        });
        
        Route::middleware('permission:users.create')->group(function () {
            Route::get('users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('users', [UserController::class, 'store'])->name('users.store');
        });
        
        Route::middleware('permission:users.edit')->group(function () {
            Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->whereNumber('user');
            Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->whereNumber('user');
        });
        
        Route::middleware('permission:users.view')->group(function () {
            Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->whereNumber('user');
            Route::get('users/{user}/export-pdf', [UserController::class, 'exportPdf'])->name('users.export-pdf')->whereNumber('user');
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
            Route::get('api/monitoring/realtime', [DashboardController::class, 'monitoringRealtime'])->name('api.monitoring.realtime');
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

        // Gerenciamento de Backups
        Route::middleware('permission:users.view')->prefix('backup')->name('backup.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BackupController::class, 'index'])->name('index');
            Route::post('/create', [\App\Http\Controllers\Admin\BackupController::class, 'create'])->name('create');
            Route::get('/download/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'download'])->name('download');
            Route::delete('/delete/{filename}', [\App\Http\Controllers\Admin\BackupController::class, 'destroy'])->name('destroy');
            Route::get('/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restoreForm'])->name('restore.form');
            Route::post('/restore', [\App\Http\Controllers\Admin\BackupController::class, 'restore'])->name('restore');
            Route::get('/stats', [\App\Http\Controllers\Admin\BackupController::class, 'stats'])->name('stats');
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

// Sistema de Mensagens de Contato (Fale Conosco) - Admin e Técnicos
Route::middleware(['auth'])->prefix('admin')->name('admin.contact.')->group(function () {
    Route::get('/contact-messages', [\App\Http\Controllers\ContactController::class, 'listMessages'])->name('list');
    Route::get('/contact-messages/{contactMessage}', [\App\Http\Controllers\ContactController::class, 'showMessage'])->name('show');
    Route::post('/contact-messages/{contactMessage}/respond', [\App\Http\Controllers\ContactController::class, 'respond'])->name('respond');
    Route::patch('/contact-messages/{contactMessage}/status', [\App\Http\Controllers\ContactController::class, 'updateStatus'])->name('updateStatus');
});

// Sistema de Mensagens Internas
Route::middleware('auth')->group(function () {
    Route::get('/messages', [\App\Http\Controllers\UserMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/compose/{user?}', [\App\Http\Controllers\UserMessageController::class, 'compose'])->name('messages.compose');
    Route::get('/messages/{message}', [\App\Http\Controllers\UserMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{messageId}/reply', [\App\Http\Controllers\UserMessageController::class, 'reply'])->name('messages.reply');
    Route::patch('/messages/{message}/read', [\App\Http\Controllers\UserMessageController::class, 'markAsRead'])->name('messages.read');
    Route::post('/messages/send', [\App\Http\Controllers\UserMessageController::class, 'store'])->name('messages.send');
    Route::post('/messages', [\App\Http\Controllers\UserMessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/mark-all-read', [\App\Http\Controllers\UserMessageController::class, 'markAllRead'])->name('messages.mark-all-read');
});

// Preferências de Notificação (restrito a administradores)
Route::middleware(['auth','admin'])->group(function () {
    Route::get('/notifications/preferences', [\App\Http\Controllers\NotificationPreferenceController::class, 'index'])->name('notifications.preferences');
    Route::put('/notifications/preferences', [\App\Http\Controllers\NotificationPreferenceController::class, 'update'])->name('notifications.preferences.update');
    Route::post('/notifications/preferences/test', [\App\Http\Controllers\NotificationPreferenceController::class, 'test'])->name('notifications.preferences.test');
    Route::get('/notifications/telegram/instructions', [\App\Http\Controllers\NotificationPreferenceController::class, 'getTelegramInstructions'])->name('notifications.telegram.instructions');
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
