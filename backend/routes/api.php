<?php

use App\Http\Controllers\Api\V1\Admin\HeroController;
use App\Http\Controllers\Api\V1\Admin\LogoBannerSlideController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\ServiceCategoryController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Auth Route start
*/

Route::group(['prefix' => 'v1/auth', 'middleware' => 'throttle:api'], function(){

    /*Register route start*/
    Route::post('/register', [AuthController::class, 'register']);
    /*Register route end*/

    /*Login route start*/
    Route::post('/login', [AuthController::class, 'login']);
    /*Login route end*/

    /*logout and refresh token route start*/
    Route::group(['middleware' => ['api', 'throttle:api']], function() {

        /*logout route start*/
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh-token', [AuthController::class, 'refreshToken']);
        /*logout route end*/
    });
    /*logout and refresh token route end*/
});

/**
 * Auth Route end
*/

/**
 * Admin api route start
*/
Route::group(['prefix' => 'v1/admin', 'middleware' => ['throttle:api']], function(){

    /*service category route start*/
    Route::get('service-category', [ServiceCategoryController::class, 'index']);
    Route::post('service-category', [ServiceCategoryController::class, 'store']);
    Route::get('service-category/{id}', [ServiceCategoryController::class, 'show']);
    Route::put('service-category/{id}', [ServiceCategoryController::class, 'update']);
    Route::delete('service-category/{id}', [ServiceCategoryController::class, 'destroy']);
    Route::patch('service-category/{id}', [ServiceCategoryController::class, 'statusUpdate']);
    /*service category route end*/

    /*Hero section route start*/
    Route::get('hero-section', [HeroController::class, 'index']);
    Route::post('hero-section', [HeroController::class, 'store']);
    Route::get('hero-section/{id}', [HeroController::class, 'show']);
    Route::put('hero-section/{id}', [HeroController::class, 'update']);
    Route::delete('hero-section/{id}', [HeroController::class, 'destroy']);
    Route::patch('hero-section/{id}', [HeroController::class, 'statusUpdate']);
    /*Hero section route end*/

    /*logo banner slide route start*/
    Route::get('logo-banner-slide', [LogoBannerSlideController::class, 'index']);
    Route::post('logo-banner-slide', [LogoBannerSlideController::class, 'store']);
    Route::get('logo-banner-slide/{id}', [LogoBannerSlideController::class, 'show']);
    Route::put('logo-banner-slide/{id}', [LogoBannerSlideController::class, 'update']);
    Route::delete('logo-banner-slide/{id}', [LogoBannerSlideController::class, 'destroy']);
    Route::patch('logo-banner-slide/{id}', [LogoBannerSlideController::class, 'statusUpdate']);
    /*logo banner slide route end*/

    /*user management -> user route start*/
    Route::get('user', [UserController::class, 'index']);
    Route::post('user', [UserController::class, 'store']);
    Route::get('user/{id}', [UserController::class, 'show']);
    Route::put('user/{id}', [UserController::class, 'update']);
    Route::delete('user/{id}', [UserController::class, 'destroy']);
    /*user management -> user route end*/

    /*user management -> role route start*/
    Route::get('role', [RoleController::class, 'index']);
    Route::post('role', [RoleController::class, 'store']);
    Route::get('role/{id}', [RoleController::class, 'show']);
    Route::put('role/{id}', [RoleController::class, 'update']);
    Route::delete('role/{id}', [RoleController::class, 'destroy']);
    /*user management -> role route end*/

    /*user management -> permission route start*/
    Route::get('permission', [PermissionController::class, 'index']);
    Route::post('permission', [PermissionController::class, 'store']);
    Route::get('permission/{id}', [PermissionController::class, 'show']);
    Route::put('permission/{id}', [PermissionController::class, 'update']);
    Route::delete('permission/{id}', [PermissionController::class, 'destroy']);
    /*user management -> permission route end*/
});

/**
 * Admin api route end
*/