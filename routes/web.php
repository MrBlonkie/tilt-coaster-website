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

Route::post('/led/{state}', [ManualControlController::class, 'ledControl']);
Route::post('/stationmotor/{state}', [ManualControlController::class, 'stationMotorControl']);
Route::post('/lifthillmotor/{state}', [ManualControlController::class, 'lifthillMotorControl']);
Route::get('/manual-control/status', [ManualControlController::class, 'status']);


//AutoControlController
Route::get('/auto-control', [AutoControlController::class, 'index'])->name('auto-control.index');

Route::post('/dispatch/go', [AutoControlController::class, 'dispatchControl']);
Route::get('/auto-control/status', [AutoControlController::class, 'status']);


//TESTING
Route::get('/test', [TestController::class, 'index'])->name('test.index');
// proxy routes naar ESP
Route::get('/esp/status', [TestController::class, 'status']);
Route::post('/esp/manual/on', [TestController::class, 'manualOn']);
Route::post('/esp/manual/off', [TestController::class, 'manualOff']);
Route::post('/esp/manual/station/on', [TestController::class, 'stationMotorOn']);
Route::post('/esp/manual/station/off', [TestController::class, 'stationMotorOff']);
Route::post('/esp/manual/lifthill/on', [TestController::class, 'lifthillMotorOn']);
Route::post('/esp/manual/lifthill/off', [TestController::class, 'lifthillMotorOff']);
Route::post('/esp/dispatch/go', [TestController::class, 'dispatchGo']);