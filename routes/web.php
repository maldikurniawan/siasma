<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KonfigurasiController;
use App\Http\Controllers\MahasiswaController;
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

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    });
});

Route::middleware(['guest:admin'])->group(function () {
    Route::get('/panel', function () {
        return view('auth.loginadmin');
    })->name('loginadmin');
    Route::post('proseslogin', [AuthController::class, 'proseslogin']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('home', [HomeController::class, 'index']);

    // Presensi
    Route::get('presensi/create', [PresensiController::class, 'create']);
    Route::post('presensi/store', [PresensiController::class, 'store']);
    Route::get('presensi/rekap', [PresensiController::class, 'rekap']);
    Route::post('presensi/cetakrekap', [PresensiController::class, 'cetakrekap']);

    // Edit Profile
    Route::get('editProfile', [PresensiController::class, 'editProfile']);
    Route::post('/presensi/{id}/updateProfile', [PresensiController::class, 'updateProfile']);

    // Histori
    Route::get('presensi/histori', [PresensiController::class, 'histori']);
    Route::post('gethistori', [PresensiController::class, 'gethistori']);

    //Izin
    Route::get('presensi/izin', [PresensiController::class, 'izin']);
    Route::get('presensi/buatizin', [PresensiController::class, 'buatizin']);
    Route::post('presensi/storeizin', [PresensiController::class, 'storeizin']);
    Route::post('presensi/cekpengajuanizin', [PresensiController::class, 'cekpengajuanizin']);
    Route::post('presensi/approveizinsakit', [PresensiController::class, 'approveizinsakit']);
    Route::get('presensi/{id}/batalkanizinsakit', [PresensiController::class, 'batalkanizinsakit']);
    Route::get('izin/{kode_izin}/showact', [PresensiController::class, 'showact']);
    Route::get('izin/{kode_izin}/edit', [PresensiController::class, 'editizin']);
    Route::post('izin/{kode_izin}/update', [PresensiController::class, 'updateizin']);
    Route::get('izin/{kode_izin}/delete', [PresensiController::class, 'deleteizin']);

    // Konfigurasi Absen
    Route::get('konfigurasi/lokasiabsen', [KonfigurasiController::class, 'lokasiabsen']);
    Route::post('konfigurasi/updatelokasi', [KonfigurasiController::class, 'updatelokasi']);

    // Konfigurasi Jam Absen
    Route::get('konfigurasi/jamabsen', [KonfigurasiController::class, 'jamabsen']);
    Route::post('konfigurasi/updatejamabsen', [KonfigurasiController::class, 'updatejamabsen']);
});

Route::middleware(['auth:admin'])->group(function () {
    Route::get('proseslogout', [AuthController::class, 'proseslogout']);
    Route::get('/panel/homeadmin', [HomeController::class, 'homeadmin']);

    // Mahasiswa
    Route::get('/mahasiswa', [MahasiswaController::class, 'index']);
    Route::post('/mahasiswa/store', [MahasiswaController::class, 'store']);
});


Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
