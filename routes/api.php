<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FundController;

Route::prefix('funds')->group(function () {
    Route::get('/', [FundController::class, 'index']);
    Route::get('/duplicates', [FundController::class, 'showDuplicates']);
    Route::post('/', [FundController::class, 'store']);
    Route::get('{id}', [FundController::class, 'show']);
    Route::put('{id}', [FundController::class, 'update']);
    Route::delete('{id}', [FundController::class, 'destroy']);
});
