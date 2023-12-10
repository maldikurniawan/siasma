<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PresensiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    });
// });

Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('home', [HomeController::class, 'index']);

// Presensi
Route::get('presensi/create', [PresensiController::class, 'create']);
Route::post('presensi/store', [PresensiController::class, 'store']);

// Edit Profile
Route::get('editProfile', [PresensiController::class, 'editProfile']);
Route::post('/presensi/{id}/updateProfile', [PresensiController::class, 'updateProfile']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
