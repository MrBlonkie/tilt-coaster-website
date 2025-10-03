<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ControlController;

Route::get('/', function () {
    return view('home');
});


//ControlController
Route::get('/control', [ControlController::class, 'index'])->name('control.index');

Route::post('/led/{state}', [ControlController::class, 'ledControl']);
Route::post('/stationmotor/{state}', [ControlController::class, 'stationMotorControl']);
Route::post('/lifthillmotor/{state}', [ControlController::class, 'lifthillMotorControl']);
Route::get('/control/status', [ControlController::class, 'status']);

