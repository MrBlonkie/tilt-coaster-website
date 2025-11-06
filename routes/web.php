<?php

use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ManualControlController;
use App\Http\Controllers\AutoControlController;
use App\Models\MqttMessage;

Route::get('/', function () {
    return view('home');
});



Route::get('/status/latest', function() {
    $station = MqttMessage::where('topic', 'station/status')->latest()->first();
    $tiltdrop = MqttMessage::where('topic', 'tiltdrop/status')->latest()->first();

    return response()->json([
        'station' => $station ? json_decode($station->message, true) : null,
        'tiltdrop' => $tiltdrop ? json_decode($tiltdrop->message, true) : null,
    ]);
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