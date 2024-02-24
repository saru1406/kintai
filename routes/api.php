<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\WorkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [LoginController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('work/start', [WorkController::class, 'start'])->name('work.start');
    Route::post('work/end', [WorkController::class, 'end'])->name('work.end');
    Route::post('work/break-start', [WorkController::class, 'breakStart'])->name('work.break_start');
    Route::post('work/break-end', [WorkController::class, 'breakEnd'])->name('work.break-end');
    Route::get('work/break-status', [WorkController::class, 'fetchBreakStatus'])->name('work.break_status');
});
