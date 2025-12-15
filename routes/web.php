<?php

use App\Http\Controllers\CallController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('calls', CallController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
Route::post('calls/{call}/reanalyze', [CallController::class, 'reanalyze'])->name('calls.reanalyze');
