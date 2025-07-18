<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PermissionController;

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
    return redirect()->route('login');
});

Auth::routes();

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
    
    // Categorias 
    Route::middleware('permission:categories.view')->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    });
    
    Route::middleware('permission:categories.manage')->group(function () {
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
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
        Route::middleware('permission:system.ldap')->group(function () {
            Route::get('/ldap/import', [\App\Http\Controllers\LdapImportController::class, 'showImportForm'])->name('ldap.import.form');
            Route::post('/ldap/preview', [\App\Http\Controllers\LdapImportController::class, 'preview'])->name('ldap.import.preview');
            Route::post('/ldap/import', [\App\Http\Controllers\LdapImportController::class, 'import'])->name('ldap.import.process');
        });
    });

    // Painel TV (verificação de permissão)
    Route::middleware('permission:board.view')->group(function () {
        Route::redirect('/painel-tv', '/painel-tv-enhanced');
        Route::get('/painel-tv-enhanced', [TicketController::class, 'boardTvEnhanced'])->name('tickets.boardTvEnhanced');
    });
});

// Redirect root to dashboard for authenticated users
Route::redirect('/home', '/dashboard');
