<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\PasswordResetController;
use App\Http\Controllers\Api\V1\RegisterController;
use App\Http\Controllers\Api\V1\RefreshTokenController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\Cors;

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

Route::post('/v1/auth/login', [LoginController::class, 'login'])
    ->middleware([
        ForceJsonResponse::class,
        Cors::class,
        'guest',
    ]);

Route::post('/v1/auth/register', [RegisterController::class, 'register'])
    ->middleware([
        ForceJsonResponse::class,
        Cors::class,
        'auth:api',
    ]);

Route::post('/v1/auth/password/reset', [PasswordResetController::class, 'update'])
    ->middleware([
        'auth:api',
        ForceJsonResponse::class,
        Cors::class,
    ]);


Route::post('/v1/auth/oauth/token', [RefreshTokenController::class, 'refresh'])
    ->middleware([
        ForceJsonResponse::class,
        Cors::class,
        'guest',
    ]);

Route::post('/v1/auth/logout', [AuthController::class, 'logout'])
    ->middleware([
        'auth:api',
        ForceJsonResponse::class,
        Cors::class,
    ]);

Route::get('/v1/auth/users', [UserController::class, 'index'])
    ->middleware([
        'auth:api',
        ForceJsonResponse::class,
        Cors::class,
    ]);

Route::delete('/v1/auth/users/{id}', [UserController::class, 'destroy'])
    ->middleware([
        'auth:api',
        ForceJsonResponse::class,
        Cors::class,
    ]);

Route::patch('/v1/auth/users/{id}', [UserController::class, 'update'])
    ->middleware([
        'auth:api',
        ForceJsonResponse::class,
        Cors::class,
    ]);
