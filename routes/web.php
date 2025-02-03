<?php

use App\Http\Controllers\LoginCT;
use App\Http\Controllers\SuperadminCT;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginCT::class, 'index'])->name('login');
Route::post('/login', [LoginCT::class, 'auth']);
Route::post('/logout', [LoginCT::class, 'logout']);

Route::middleware(['auth','superadmin', 'revalidate'])->prefix('/superadmin')->group(function (){
    Route::get('/', [SuperadminCT::class, 'index']);
});


Route::get('/', function () {
    return view('pages.dashboard');
});
