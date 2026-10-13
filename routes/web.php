<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DaftarBelanjaController;
use App\Livewire\Auth\Login;
use App\Livewire\RekomendasiMenu;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('/login', Login::class)->middleware('guest')->name('login');

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/rekomendasi', RekomendasiMenu::class)->name('rekomendasi.index');
    Route::get('/rekomendasi/{rekomendasi}/cetak', [DaftarBelanjaController::class, 'cetak'])->name('rekomendasi.cetak');
    Route::get('/rekomendasi/{rekomendasi}/csv', [DaftarBelanjaController::class, 'csv'])->name('rekomendasi.csv');
    Route::view('/dokumentasi/metode', 'dokumentasi.metode')->name('dokumentasi.metode');
});
