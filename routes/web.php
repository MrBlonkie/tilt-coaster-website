<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ControlController;

Route::get('/', function () {
    return view('home');
});


//ControlController
Route::get('/control', [ControlController::class, 'index'])->name('control.index');