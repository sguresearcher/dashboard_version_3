<?php

use App\Http\Controllers\getDataTenantCT;
use App\Http\Controllers\LoginCT;
use App\Http\Controllers\MonitoringSensorCT;
use App\Http\Controllers\SuperadminCT;
use App\Http\Controllers\UsersCT;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
});

Route::get('/login', [LoginCT::class, 'index'])->name('login');
Route::post('/login', [LoginCT::class, 'auth']);
Route::post('/logout', [LoginCT::class, 'logout']);

Route::middleware(['auth', 'superadmin', 'revalidate'])->prefix('/superadmin')->group(function () {
    Route::get('/', [SuperadminCT::class, 'index']);
    Route::get('/monitor', [MonitoringSensorCT::class, 'index']);
});

Route::middleware(['auth', 'tenant', 'revalidate'])->group(function () {
    Route::get('/home', [UsersCT::class, 'index']);
    Route::get('/sensor/{sensor}', [UsersCT::class, 'bySensor']);
    Route::get('/setting', [UsersCT::class, 'setting']);
    Route::get('/monitor', [MonitoringSensorCT::class, 'index']);
});
