<?php

use App\Http\Controllers\GuestCT;
use App\Http\Controllers\LoginCT;
use App\Http\Controllers\SuperadminCT;
use App\Http\Controllers\UsersCT;
use Illuminate\Support\Facades\Route;



Route::get('/login', [LoginCT::class, 'index'])->name('login');
Route::post('/login', [LoginCT::class, 'auth']);
Route::post('/logout', [LoginCT::class, 'logout']);

Route::middleware(['auth','superadmin', 'revalidate'])->prefix('/superadmin')->group(function (){
    Route::get('/', [SuperadminCT::class, 'index']);
});

Route::middleware(['auth','revalidate'])->group(function(){
    Route::get('/', [UsersCT::class, 'index']);
    Route::get('/sensor/{sensor}', [UsersCT::class, 'bySensor']);
    Route::get('/setting', [UsersCT::class, 'setting']);
});

Route::get('/test/api', [GuestCT::class, 'index']);

Route::get('/', function () {
    return view('pages.dashboard');
});
