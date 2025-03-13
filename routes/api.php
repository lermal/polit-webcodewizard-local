<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MapController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // Users
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'getUsers']);
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'show']);
    Route::put('/users/{user}', [App\Http\Controllers\Admin\UserController::class, 'update']);

    // Groups
    Route::get('/groups', [App\Http\Controllers\Admin\GroupController::class, 'index']);
    Route::post('/groups', [App\Http\Controllers\Admin\GroupController::class, 'store']);
    Route::delete('/groups/{group}', [App\Http\Controllers\Admin\GroupController::class, 'destroy']);
    Route::get('/teachers/{teacher}/groups', [App\Http\Controllers\Admin\GroupController::class, 'getTeacherGroups']);
});

// Публичные маршруты для карты (не требуют аутентификации)
Route::group(['middleware' => ['web']], function () {
    // Маркеры
    Route::get('/markers', [MapController::class, 'getMarkers']);

    // Маршруты
    Route::get('/routes/{id}/view', [MapController::class, 'getRoute']);
});

// Защищенные маршруты для карты
Route::middleware(['auth:sanctum', 'web'])->group(function () {
    // Маркеры (создание, обновление, удаление)
    Route::post('/markers', [MapController::class, 'storeMarker']);
    Route::put('/markers/{marker}', [MapController::class, 'updateMarker']);
    Route::delete('/markers/{marker}', [MapController::class, 'deleteMarker']);

    // Маршруты (создание, обновление, удаление)
    Route::post('/routes', [MapController::class, 'storeRoute']);
    Route::put('/routes/{id}', [MapController::class, 'updateRoute']);
    Route::delete('/routes/{id}', [MapController::class, 'deleteRoute']);
});
