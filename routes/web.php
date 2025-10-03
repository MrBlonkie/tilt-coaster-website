<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ManualControlController;
use App\Http\Controllers\AutoControlController;

Route::get('/', function () {
    return view('home');
});


//ManualControlController
Route::get('/manual-control', [ManualControlController::class, 'index'])->name('manual-control.index');

Route::post('/led/{state}', [ManualControlController::class, 'ledControl']);
Route::post('/stationmotor/{state}', [ManualControlController::class, 'stationMotorControl']);
Route::post('/lifthillmotor/{state}', [ManualControlController::class, 'lifthillMotorControl']);
Route::get('/manual-control/status', [ManualControlController::class, 'status']);


//AutoControlController
Route::get('/auto-control', [AutoControlController::class, 'index'])->name('auto-control.index');