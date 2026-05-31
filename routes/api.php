<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TemplateController;

Route::middleware('auth')->group(function () {
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/templates/{template}', [TemplateController::class, 'show']);
    Route::post('/templates', [TemplateController::class, 'store'])->middleware('role:administrator|staff');
    Route::post('/templates/{template}/versions', [TemplateController::class, 'uploadVersion'])->middleware('role:administrator|staff');
});


