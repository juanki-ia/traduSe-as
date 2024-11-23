<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LearningController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;





Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);




Route::middleware(['auth:sanctum'])->group(function () {
    //Users
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/learning', [LearningController::class,'get']);

});
