<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;
use Auth;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/showLogin', [UserController::class, 'showLoginForm'])->name('showLogin');
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::group(['middleware' => ['auth']], function () { 
    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::get('/product-create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product-store', [ProductController::class, 'store'])->name('product.store');
    Route::post('/product-delete', [ProductController::class, 'destroy'])->name('product.destroy');
    Route::post('/product-edit', [ProductController::class, 'edit'])->name('product.edit');
    
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user-create', [UserController::class, 'create'])->name('user.create');
    Route::post('/user-store', [UserController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::post('permission/get', [UserController::class,'getAllPermissions'])->name('getPermission');
    Route::post('permission/save', [UserController::class,'assignPermissions'])->name('assignPermissions');
    Route::get('roles', [UserController::class, 'getAllRoles']);
    Route::put('user/{id}', [UserController::class,'store'])->name('user.udpate');
    Route::get('user/{id}/destroy', [UserController::class,'destroy'])->name('user.destroy');


Route::get('/permission', [PermissionController::class, 'index'])->name('permission.index');
Route::get('/permission-create', [PermissionController::class, 'create'])->name('permission.create');
Route::get('/permission-show', [PermissionController::class, 'show'])->name('show.permission');


});