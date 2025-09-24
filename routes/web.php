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
Route::get('api/users', [UserController::class, 'apiIndex'])->name('users.api.index');
Route::post('/user/create', action: [UserController::class, 'apiCreate'])->name('users.api.create');
Route::get('api/groups', [GroupController::class, 'apiIndex'])->name('groups.api.index');
Route::delete('api/users/{user}', [UserController::class, 'apiDestroy'])->name('users.api.destroy');

Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
Route::post('/users/import/process', [UserController::class, 'processImport'])->name('users.import.process');
Route::get('/users/import', [UserController::class, 'importView'])->name('users.import');


Route::get('/users', [UserController::class, 'indexView'])->name('users.index');
Route::get('/users/create', [UserController::class, 'createView'])->name('users.create');
Route::get('/users/{uuid}/edit', [UserController::class, 'editView'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');




// Groups
Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
Route::get('/groups/{group}/edit', [GroupController::class, 'edit'])->name('groups.edit');
Route::put('/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
Route::delete('/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');

// Permissions
Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
Route::post('/permissions/groups/{group}/assign', [PermissionController::class, 'assignPrivilegesToGroup'])
    ->name('permissions.assign');




//Firewall

Route::get('/firewall', [FirewallController::class, 'index'])->name('firewall.index');








//});




