<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Opnsense\Auth\UserController;
use App\Http\Controllers\Opnsense\Auth\GroupController;
use App\Http\Controllers\Opnsense\Auth\PermissionController;
use App\Http\Controllers\Opnsense\Firewall\FirewallController;
use PHPUnit\Framework\Attributes\Group;

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

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

// Processa login
Route::post('/login', [LoginController::class, 'login']);

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

//Route::middleware(['auth'])->group(function () {
// Users
//api

// Importação via Excel (novo)
Route::get('/users/import/excel/template', [UserController::class, 'downloadExcelTemplate'])->name('users.import.excel.template');
Route::post('/users/import/excel/process', [UserController::class, 'processExcelImport'])->name('users.import.excel.process');
Route::get('/users/import/excel/credentials-pdf', [UserController::class, 'downloadCredentialsPdf'])->name('users.import.credentials.pdf');

// Importação via CSV (legado)
Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
Route::post('/users/import/process', [UserController::class, 'processImport'])->name('users.import.process');
Route::get('/users/import', [UserController::class, 'importView'])->name('users.import');


Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::get('/users/{uuid}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{uuid}', [UserController::class, 'update'])->name('users.update');
Route::get('api/users', [UserController::class, 'apiIndex'])->name('users.api.index');
Route::post('/user/create', action: [UserController::class, 'store'])->name('users.api.create');
Route::delete('api/users/{user}', [UserController::class, 'destroy'])->name('users.api.destroy');



// Groups
Route::get('/groups', [GroupController::class, 'indexView'])->name('groups.index');
Route::get('/groups/create', [GroupController::class, 'createView'])->name('groups.create');
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
Route::delete('api/groups/{group}', [GroupController::class, 'destroy'])->name('groups.api.destroy');
Route::get('api/groups', [GroupController::class, 'index'])->name('groups.api.index');
Route::get('/groups/{group}/export-users', [GroupController::class, 'exportUsers'])->name('groups.export.users');



// Permissions
Route::get('/permissions', [PermissionController::class, 'indexView'])->name('permissions.index');
Route::post('/permissions/groups/{group}/assign', [PermissionController::class, 'assignPrivilegesToGroup'])
    ->name('permissions.assign');

Route::get('api/permissions', [PermissionController::class, 'index'])->name('permission.api.index');




//Firewall

Route::get('/firewall', [FirewallController::class, 'index'])->name('firewall.index');








//});




