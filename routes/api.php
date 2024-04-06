<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\BreakTimeController;
use App\Http\Controllers\Api\CsvWorkController;
use App\Http\Controllers\Api\WorkController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PdfController;
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

// Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::middleware('auth:sanctum')->group(function () {
    // 勤怠
    Route::post('work/start', [WorkController::class, 'start'])->name('work.start');
    Route::post('work/end', [WorkController::class, 'end'])->name('work.end');
    Route::get('works', [WorkController::class, 'index'])->name('works');
    Route::get('work', [WorkController::class, 'show'])->name('work');

    // 休憩
    Route::post('work/break-start', [BreakTimeController::class, 'breakStart'])->name('work.break_start');
    Route::post('work/break-end', [BreakTimeController::class, 'breakEnd'])->name('work.break-end');
    Route::get('work/break-status', [BreakTimeController::class, 'fetchBreakStatus'])->name('work.break_status');

    // 勤怠CSV
    Route::get('works/csv', CsvWorkController::class)->name('works.csv_export');

    // 勤怠PDF
    Route::get('works/pdf', PdfController::class)->name('work.pdf');
});
