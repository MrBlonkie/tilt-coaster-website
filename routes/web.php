<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ControlController;

Route::get('/', function () {
    return view('home');
});


//ControlController
Route::get('/control', [ControlController::class, 'index'])->name('control.index');

Route::post('/control/{state}', [ControlController::class, 'toggle'])
    ->where('state', 'on|off');


Route::get('/esp/status', [ControlController::class, 'espStatus']);
