<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ManualControlController;
use App\Http\Controllers\AutoControlController;

Route::get('/', function () {
    return view('home');
});


//ManualControlController
Route::get('/manual-control', [ManualControlController::class, 'index'])->name('manual-control.index');
//motors
Route::post('/manual/stationmotor/{state}', [ManualControlController::class, 'stationMotorControl']);
Route::post('/manual/lifthillmotor/{state}', [ManualControlController::class, 'lifthillMotorControl']);
Route::post('/manual/tiltdropmotor/{state}', [ManualControlController::class, 'tiltdropMotorControl']);
Route::post('/manual/releasedropmotor/{state}', [ManualControlController::class, 'releasedropMotorControl']);
//other
Route::get('/manual-control/status', [ManualControlController::class, 'status']);
Route::get('/manual/{state}', [ManualControlController::class, 'manualMode']);


//AutoControlController
Route::get('/auto-control', [AutoControlController::class, 'index'])->name('auto-control.index');

Route::post('/dispatch/go', [AutoControlController::class, 'dispatchControl']);
Route::get('/auto-control/status', [AutoControlController::class, 'status']);

// TiltDrop routes via AutoControlController
Route::post('/tiltdrop/open', [AutoControlController::class, 'tiltdropOpen']);
Route::post('/tiltdrop/close', [AutoControlController::class, 'tiltdropClose']);
Route::post('/tiltdrop/drop', [AutoControlController::class, 'tiltdropDrop']);
Route::get('/tiltdrop/status', [AutoControlController::class, 'tiltdropStatus']);