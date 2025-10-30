<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Opnsense\Auth\UserController;
use App\Http\Controllers\Opnsense\Auth\GroupController;
use App\Http\Controllers\Opnsense\Auth\PermissionController;
use App\Http\Controllers\Opnsense\AliasController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========================================
// Rotas Públicas (Sem Autenticação)
// ========================================

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// ========================================
// Rotas Protegidas (Com Autenticação)
// ========================================

Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.alternative');
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');

    // ========================================
    // Users - Gerenciamento de Usuários
    // ========================================


    // Views
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::get('/users/{uuid}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/import', [UserController::class, 'importView'])->name('users.import');

    // Actions
    Route::post('/user/create', [UserController::class, 'store'])->name('users.api.create');
    Route::put('/users/{uuid}', [UserController::class, 'update'])->name('users.update');
    Route::delete('api/users/{user}', [UserController::class, 'destroy'])->name('users.api.destroy');

    // Import - Excel (novo)
    Route::get('/users/import/excel/template', [UserController::class, 'downloadExcelTemplate'])->name('users.import.excel.template');
    Route::post('/users/import/excel/process', [UserController::class, 'processExcelImport'])->name('users.import.excel.process');
    Route::get('/users/import/excel/credentials-pdf', [UserController::class, 'downloadCredentialsPdf'])->name('users.import.credentials.pdf');

    // Import - CSV (legado)
    Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
    Route::post('/users/import/process', [UserController::class, 'processImport'])->name('users.import.process');

    // API
    Route::get('api/users', [UserController::class, 'apiIndex'])->name('users.api.index');

    // ========================================
    // Groups - Gerenciamento de Grupos
    // ========================================

    // Views
    Route::get('/groups', [GroupController::class, 'indexView'])->name('groups.index');
    Route::get('/groups/create', [GroupController::class, 'createView'])->name('groups.create');
    Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');

    // Actions
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('api/groups/{group}', [GroupController::class, 'destroy'])->name('groups.api.destroy');

    // Export
    Route::get('/groups/{group}/export-users', [GroupController::class, 'exportUsers'])->name('groups.export.users');

    // API
    Route::get('api/groups', [GroupController::class, 'index'])->name('groups.api.index');

    // ========================================
    // Permissions - Gerenciamento de Permissões
    // ========================================

    // Views
    Route::get('/permissions', [PermissionController::class, 'indexView'])->name('permissions.index');

    // Actions
    Route::post('/permissions/groups/{group}/assign', [PermissionController::class, 'assignPrivilegesToGroup'])->name('permissions.assign');

    // API
    Route::get('api/permissions', [PermissionController::class, 'index'])->name('permission.api.index');

    // ========================================
    // Aliases
    // ========================================

    // Views
    Route::get('/aliases', [AliasController::class, 'index'])->name('aliases.index');

    // API - Aliases (CRUD)
    Route::get('api/aliases', [AliasController::class, 'list'])->name('api.aliases.list');
    Route::get('api/aliases/{uuid}', [AliasController::class, 'get'])->name('api.aliases.get');
    Route::post('api/aliases', [AliasController::class, 'create'])->name('api.aliases.create');
    Route::put('api/aliases/{uuid}', [AliasController::class, 'update'])->name('api.aliases.update');
    Route::delete('api/aliases/{uuid}', [AliasController::class, 'delete'])->name('api.aliases.delete');
    Route::post('api/aliases/apply', [AliasController::class, 'applyChanges'])->name('api.aliases.apply');

});



