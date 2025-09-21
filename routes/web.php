<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Opnsense\UserController;
use App\Http\Controllers\Opnsense\GroupController;
use App\Http\Controllers\Opnsense\PermissionController;


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



Route::get('/users', [UserController::class, 'indexView'])->name('users.index');
Route::get('/users/create', [UserController::class, 'createView'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

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
//});
