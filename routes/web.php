<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Admin\HolidayAdminController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\HomeController; 
use App\Http\Controllers\Admin\UserAdminController;

Route::get('/dashboard/{guid}', [DashboardController::class, 'index'])->middleware('guidauth');
Route::post('/booking/{guid}', [DashboardController::class, 'book'])->middleware('guidauth');
Route::delete('/booking/{guid}/{id}', [DashboardController::class, 'delete'])->middleware('guidauth');

Route::get('/calendar/{guid}', [CalendarController::class, 'events'])->middleware('guidauth');
Route::get('/calendar/{guid}/holidays', [CalendarController::class, 'holidays'])->middleware('guidauth');

Route::middleware(['adminsecret'])->prefix('admin')->group(function () {
    Route::get('holidays', [HolidayAdminController::class, 'index']);
    Route::get('holidays/create', [HolidayAdminController::class, 'create']);
    Route::post('holidays', [HolidayAdminController::class, 'store']);
    Route::get('holidays/{id}/edit', [HolidayAdminController::class, 'edit']);
    Route::put('holidays/{id}', [HolidayAdminController::class, 'update']);
    Route::delete('holidays/{id}', [HolidayAdminController::class, 'destroy']);

    Route::get('export/csv',        [ExportController::class, 'csv']);
    Route::get('export/week',       [ExportController::class, 'weekPdf']);
    Route::get('export/user-csv',   [ExportController::class, 'userCsv']);

    Route::get('users',               [UserAdminController::class, 'index']);
    Route::get('users/create',        [UserAdminController::class, 'create']);
    Route::post('users',              [UserAdminController::class, 'store']);          // Einzel-Einladung
    Route::post('users/resend/{id}',  [UserAdminController::class, 'resend']);         // Einladung erneut
    Route::post('users/import',       [UserAdminController::class, 'import']);         // CSV-Import
    Route::delete('users/{id}',       [UserAdminController::class, 'destroy']);        // optional
});

Route::get('/admin/login', [HomeController::class, 'loginForm']);
Route::post('/admin/login', [HomeController::class, 'login']);
Route::post('/admin/logout', [HomeController::class, 'logout'])->middleware('adminsecret');

// Zentrale Admin-Startseite (Session oder ?key=... erforderlich)
Route::get('/admin', [HomeController::class, 'index'])->middleware('adminsecret');

