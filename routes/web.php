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


//AutoControlController
Route::get('/auto-control', [AutoControlController::class, 'index'])->name('auto-control.index');


