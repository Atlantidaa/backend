<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\YoutubeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        'prefix' => 'youtube',
    ],
    function() {
        Route::get('search', [YoutubeController::class, 'search'])->name('youtubeSearch');
        Route::get('download/{id}', [YoutubeController::class, 'download'])->name('youtubeDownload');
    }
);
