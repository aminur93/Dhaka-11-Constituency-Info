<?php

use App\Http\Controllers\Api\V1\Admin\DistrictController;
use App\Http\Controllers\Api\V1\Admin\DivisionController;
use App\Http\Controllers\Api\V1\Admin\HeroController;
use App\Http\Controllers\Api\V1\Admin\LogoBannerSlideController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\ServiceCategoryController;
use App\Http\Controllers\Api\V1\Admin\ThanaController;
use App\Http\Controllers\Api\V1\Admin\UnionController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Admin\WardController;
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

    /*ward route start*/
    Route::get('ward', [WardController::class, 'index']);
    Route::post('ward', [WardController::class, 'store']);
    Route::get('ward/{id}', [WardController::class, 'show']);
    Route::put('ward/{id}', [WardController::class, 'update']);
    Route::delete('ward/{id}', [WardController::class, 'destroy']);
    Route::patch('ward/status-update/{id}', [WardController::class, 'updateStatus']);
    /*ward route end*/

    /*union route start*/
    Route::get('union', [UnionController::class, 'index']);
    Route::post('union', [UnionController::class, 'store']);
    Route::get('union/{id}', [UnionController::class, 'show']);
    Route::put('union/{id}', [UnionController::class, 'update']);
    Route::delete('union/{id}', [UnionController::class, 'destroy']);
    Route::patch('union/status-update/{id}', [UnionController::class, 'updateStatus']);
    /*union route end*/

    /*thana route start*/
    Route::get('thana', [ThanaController::class, 'index']);
    Route::post('thana', [ThanaController::class, 'store']);
    Route::get('thana/{id}', [ThanaController::class, 'show']);
    Route::put('thana/{id}', [ThanaController::class, 'update']);
    Route::delete('thana/{id}', [ThanaController::class, 'destroy']);
    Route::patch('thana/status-update/{id}', [ThanaController::class, 'updateStatus']);
    /*thana route end*/

    /*District route start*/
    Route::get('district', [DistrictController::class, 'index']);
    Route::get('district/get-all-district-division', [DistrictController::class, 'getAllDistrictWithDivision']);
    Route::post('district', [DistrictController::class, 'store']);
    Route::get('district/{id}', [DistrictController::class, 'show']);
    Route::put('district/{id}', [DistrictController::class, 'update']);
    Route::delete('district/{id}', [DistrictController::class, 'destroy']);
    Route::patch('district/status-update/{id}', [DistrictController::class, 'updateStatus']);
    /*District route end*/

    /*division route start*/
    Route::get('division', [DivisionController::class, 'index']);
    Route::post('division', [DivisionController::class, 'store']);
    Route::get('division/{id}', [DivisionController::class, 'show']);
    Route::put('division/{id}', [DivisionController::class, 'update']);
    Route::delete('division/{id}', [DivisionController::class, 'destroy']);
    Route::patch('division/status-update/{id}', [DivisionController::class, 'updateStatus']);
    /*division route end*/

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