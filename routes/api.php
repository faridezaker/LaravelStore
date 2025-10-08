<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;


Route::prefix('v1')
    ->group(function () {

        Route::apiResource('brands', BrandController::class)->except(['index', 'show'])->middleware(['auth:api',RoleMiddleware::class . ':admin']);
        Route::get('brands', [BrandController::class,'index']);
        Route::get('brands/{id}', [BrandController::class,'show']);


        Route::apiResource('categories', CategoryController::class)->except(['index', 'show'])->middleware(['auth:api',RoleMiddleware::class . ':admin']);
        Route::get('categories', [CategoryController::class,'index']);
        Route::get('categories/{id}', [CategoryController::class,'show']);
        Route::get('categories/{category}/children', [CategoryController::class, 'getChildren']);
        Route::get('categories/{category}/parent', [CategoryController::class, 'getParent']);
    });

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

