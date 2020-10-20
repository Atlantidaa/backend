<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\YoutubeController;
use App\Http\Controllers\VkController;
use App\Http\Controllers\LastfmController;
use App\Http\Controllers\AuthController;

Route::prefix('user')->group(function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('youtube')->group(function() {
    Route::get('search', [YoutubeController::class, 'search']);
    Route::get('download/{id}', [YoutubeController::class, 'download']);
});

Route::prefix('vk')->group(function() {
    Route::get('search', [VkController::class, 'search']);
});

Route::prefix('lastfm')->group(function() {
    Route::get('hints', [LastfmController::class, 'hints']);
});

