<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;



Route::get('/', function () { return view('upload'); });
Route::post('/generate-pdf', [ReportController::class, 'generate'])->name('generate.pdf');
