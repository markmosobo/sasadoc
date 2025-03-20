<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [ChatbotController::class, 'login']);
Route::post('/register', [ChatbotController::class, 'register']);
// Route::post('/logout', [ChatbotController::class, 'logout']);
Route::post('/chat', [ChatbotController::class, 'chat']);

Route::middleware('auth:sanctum')->post('/logout', [ChatbotController::class, 'logout']);
