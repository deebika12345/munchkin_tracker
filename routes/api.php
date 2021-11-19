<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/get-parents', [AuthController::class, 'getParents']);
    Route::get('/get-drivers', [AuthController::class, 'getDrivers']);
    Route::post('/assign-driver', [AuthController::class, 'assignDriver']);
    Route::post('/tracking-update', [AuthController::class, 'trackingUpdate']);
    Route::get('/get-driver-tracking', [AuthController::class, 'getDriverTracking']);
    Route::post('/update-arriving-time', [AuthController::class, 'updateArrivingTime']);
    Route::post('/update-dismissal', [AuthController::class, 'updateDismissal']);
    Route::delete('delete-user/{id}', [AuthController::class, 'deleteUser']);
});
