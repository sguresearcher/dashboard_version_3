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

Route::middleware(['auth','superadmin', 'revalidate'])->prefix('/superadmin')->group(function (){
    Route::get('/', [SuperadminCT::class, 'index']);
    Route::get('/monitor', [MonitoringSensorCT::class, 'index']);
});

Route::middleware(['auth','revalidate'])->group(function(){
    Route::get('/home', [UsersCT::class, 'index']);
    Route::get('/sensor/{sensor}', [UsersCT::class, 'bySensor']);
    Route::get('/setting', [UsersCT::class, 'setting']);
    Route::get('/monitor', [MonitoringSensorCT::class, 'index']);
});

Route::get('/data/{sensor}/h', [getDataTenantCT::class, 'totalAttack']);
Route::get('/data/{sensor}/top-10-ip', [getDataTenantCT::class, 'top10AttackerIp']);
Route::get('/data/{sensor}/total', [getDataTenantCT::class, 'total']);
Route::get('/data/{sensor}/doughnut-chart', [getDataTenantCT::class, 'getDataDoughnutChart']);
Route::get('/data/guest/total-attack', [getDataTenantCT::class, 'totalAttackGuest']);
Route::get('/data/guest/get-total-attack-sensor-average', [getDataTenantCT::class, 'getDataTotalAttackAverageGuestDashboard']);
Route::get('/data/guest/top-10', [getDataTenantCT::class, 'getDataTop10Table']);
Route::get('/data/tenant/total-attack', [getDataTenantCT::class, 'totalAttackTenantDashboard']);
Route::get('/data/tenant/top-10', [getDataTenantCT::class, 'getDataTop10TenantDashboard']);
Route::get('/data/tenant/average', [getDataTenantCT::class, 'getDataAverageTenant']);
Route::get('/data/guest/get-attack-sensor', [getDataTenantCT::class, 'getSensorAttackCount']);
Route::get('/data/guest/get-attack-sensor-average', [getDataTenantCT::class, 'getSensorAverageAttackCount']);
Route::get('/data/guest/get-attack-sensor-count', [getDataTenantCT::class, 'getSensorAttackCount']);
// Route::get('/data/guest/top10/source-ip', [getDataTenantCT::class, 'getTop10SourceIpGuest']);
Route::get('/data/tenant/get-attack-sensor-count', [getDataTenantCT::class, 'getSensorAttackCountTenant']);

Route::get('/data/tenant/sensor/{sensor}/detail', [getDataTenantCT::class, 'getAttackCountBySensorName']);
Route::get('/data/tenant/sensor/{sensor}/top10', [getDataTenantCT::class, 'getTop10AttackersBySensor']);
Route::get('/data/tenant/monitor', [MonitoringSensorCT::class, 'getSensorStatusJson']);
Route::get('/data/all-tenant/monitor', [MonitoringSensorCT::class, 'getSensorAllTenantStatus']);






