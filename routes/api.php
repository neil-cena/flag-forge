<?php

use App\Http\Controllers\Api\ExperimentEventController;
use App\Http\Controllers\Api\SdkEvaluationController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('sdk.auth')->group(function () {
    Route::post('/evaluate', SdkEvaluationController::class)->name('api.evaluate');
    Route::post('/experiments/events', [ExperimentEventController::class, 'store'])->name('api.experiments.events.store');
});
