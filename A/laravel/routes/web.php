<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SensorAlertController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
})->name('home');

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/sensor-alerts/import', [SensorAlertController::class, 'showImportForm'])->name('sensor-alerts.import.show');
    Route::post('/sensor-alerts/import', [SensorAlertController::class, 'import'])->name('sensor-alerts.import');

    Route::get('/sensor-alerts', [SensorAlertController::class, 'index'])->name('sensor-alerts.index');
    Route::patch('/sensor-alerts/{sensorAlert}/status', [SensorAlertController::class, 'updateStatus'])->name('sensor-alerts.status');

    Route::delete('/sensor-alerts/{sensorAlert}', [SensorAlertController::class, 'destroy'])
        ->middleware('admin')
        ->name('sensor-alerts.destroy');
});
