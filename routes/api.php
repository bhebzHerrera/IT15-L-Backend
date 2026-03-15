<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\SchoolDayController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/dashboard', [DashboardController::class, 'frontend']);
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/trends', [DashboardController::class, 'trends']);
    Route::get('/dashboard/calendar', [DashboardController::class, 'calendar']);
    Route::get('/enrollments', [EnrollmentController::class, 'index']);
    Route::get('/enrollments/status-summary', [EnrollmentController::class, 'statusSummary']);
    Route::post('/enrollments', [EnrollmentController::class, 'store']);
    Route::delete('/enrollments', [EnrollmentController::class, 'destroy']);
    Route::get('/enrollments/pipeline', [MetricsController::class, 'enrollmentPipeline']);
    Route::get('/reports/cards', [MetricsController::class, 'reportCards']);

    Route::apiResource('students', StudentController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('school-days', SchoolDayController::class);
    Route::apiResource('announcements', AnnouncementController::class);
});
