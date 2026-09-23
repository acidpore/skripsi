<?php

use App\Http\Controllers\DaftarBelanjaController;
use App\Livewire\Auth\Login;
use App\Livewire\RekomendasiMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', Login::class)->middleware('guest')->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/login');
})->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/rekomendasi', RekomendasiMenu::class)->name('rekomendasi.index');
    Route::get('/rekomendasi/{rekomendasi}/cetak', [DaftarBelanjaController::class, 'cetak'])->name('rekomendasi.cetak');
    Route::get('/rekomendasi/{rekomendasi}/csv', [DaftarBelanjaController::class, 'csv'])->name('rekomendasi.csv');
    Route::view('/dokumentasi/metode', 'dokumentasi.metode')->name('dokumentasi.metode');
});
