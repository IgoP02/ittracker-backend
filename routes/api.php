<?php

use App\Http\Controllers\Auth\AuthController;
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
Route::get('/hi', function () {
    return "hi";
});
Route::post('/submit', [MessageController::class, "store"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post('/register', [AuthController::class, "register"]);
    Route::get('/logout', [AuthController::class, "logout"]);

    Route::prefix("/messages")->group(function () {
        Route::get('/', [MessageController::class, "index"]);
        Route::post('/create', [MessageController::class, "store"]);
        Route::post('/delete', [MessageController::class, "destroy"]);
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
        Route::patch("/update/{id}", [ReportController::class, "update"]);
        Route::get("/stats/{field}", [ReportController::class, "getStats"]);
        Route::get("/user", [AuthController::class, "getUser"]);
    });

    //form populating routes

    //auth test

});
