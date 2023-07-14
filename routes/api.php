<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

//auth routes
Route::post('/login', [AuthController::class, "login"]);
Route::post('/submit', [MessageController::class, "store"]);
Route::get('/messages', [MessageController::class, "index"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post('/register', [AuthController::class, "register"]);
    Route::get('/logout', [AuthController::class, "logout"]);
    Route::get('/user', [AuthController::class, "getUser"]);

    Route::prefix("logs")->group(function () {
        Route::get("/", [LogsController::class, "index"]);
        Route::delete("/", [LogsController::class, "clear"]);

    });
    Route::prefix("/messages")->group(function () {
        Route::get('/latest', [MessageController::class, "getLatestMessage"]);
        Route::post('/create', [MessageController::class, "store"]);
        Route::get('/delete', [MessageController::class, "destroyLatestMessage"]);
    });
});

//report routes
Route::prefix('/reports')->group(function () {
    Route::get("/get/{id}", [ReportController::class, "show"]);
    Route::get("/field/{field}", [ReportController::class, "getField"]);
    Route::get("/issues/{type_id}", [ReportController::class, "getIssues"]);
    Route::post("/create", [ReportController::class, "store"]);

    Route::middleware("auth:sanctum")->group(function () {
        Route::get("/", [ReportController::class, "index"]);
        Route::get("/own_reports", [ReportController::class, "getOwnAssignedReports"]);
        Route::delete("/clear", [ReportController::class, "clear"]);
        Route::patch("/update/{id}", [ReportController::class, "update"]);
        Route::get("/stats/{field}", [ReportController::class, "getStats"]);
    });

});
