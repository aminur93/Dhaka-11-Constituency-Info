<?php

use App\Http\Controllers\Api\V1\Admin\AreaDemographicController;
use App\Http\Controllers\Api\V1\Admin\DistrictController;
use App\Http\Controllers\Api\V1\Admin\DivisionController;
use App\Http\Controllers\Api\V1\Admin\EventController;
use App\Http\Controllers\Api\V1\Admin\EventRegistrationController;
use App\Http\Controllers\Api\V1\Admin\FieldReportController;
use App\Http\Controllers\Api\V1\Admin\HeroController;
use App\Http\Controllers\Api\V1\Admin\LogoBannerSlideController;
use App\Http\Controllers\Api\V1\Admin\NoticeController;
use App\Http\Controllers\Api\V1\Admin\PermissionController;
use App\Http\Controllers\Api\V1\Admin\PollController;
use App\Http\Controllers\Api\V1\Admin\RoleController;
use App\Http\Controllers\Api\V1\Admin\ServiceApplicantController;
use App\Http\Controllers\Api\V1\Admin\ServiceCategoryController;
use App\Http\Controllers\Api\V1\Admin\ThanaController;
use App\Http\Controllers\Api\V1\Admin\UnionController;
use App\Http\Controllers\Api\V1\Admin\UserController;
use App\Http\Controllers\Api\V1\Admin\VolunteerAreaAssignmentController;
use App\Http\Controllers\Api\V1\Admin\volunteerController;
use App\Http\Controllers\Api\V1\Admin\VolunteerTaskController;
use App\Http\Controllers\Api\V1\Admin\WardCommissionerController;
use App\Http\Controllers\Api\V1\Admin\WardController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
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

    /*poll route start*/
    Route::get('poll', [PollController::class, 'index']);
    Route::post('poll', [PollController::class, 'store']);
    Route::get('poll/{id}', [PollController::class, 'show']);
    Route::put('poll/{id}', [PollController::class, 'update']);
    Route::patch('poll/{id}', [PollController::class, 'changeStatus']);
    Route::delete('poll/{id}', [PollController::class, 'destroy']);
    /*poll route end*/

    /*Event registration route start*/
    Route::get('event-registration', [EventRegistrationController::class, 'index']);
    Route::post('event-registration', [EventRegistrationController::class, 'store']);
    Route::get('event-registration/{id}', [EventRegistrationController::class, 'show']);
    Route::put('event-registration/{id}', [EventRegistrationController::class, 'update']);
    Route::patch('event-registration/{id}', [EventRegistrationController::class, 'changeStatus']);
    Route::delete('event-registration/{id}', [EventRegistrationController::class, 'destroy']);
    /*Event registration route end*/

    /*event route start*/
    Route::get('event', [EventController::class, 'index']);
    Route::post('event', [EventController::class, 'store']);
    Route::get('event/{id}', [EventController::class, 'show']);
    Route::put('event/{id}', [EventController::class, 'update']);
    Route::delete('event/{id}', [EventController::class, 'destroy']);
    Route::patch('event/change-status/{id}', [EventController::class, 'changeStatus']);
    /*event route end*/

    /*notice route start*/
    Route::get('notice', [NoticeController::class, 'index']);
    Route::post('notice', [NoticeController::class, 'store']);
    Route::get('notice/{id}', [NoticeController::class, 'show']);
    Route::put('notice/{id}', [NoticeController::class, 'update']);
    Route::delete('notice/{id}', [NoticeController::class, 'destroy']);
    /*notice route end*/

    /*field report route start*/
    Route::get('field-report', [FieldReportController::class, 'index']);
    Route::post('field-report', [FieldReportController::class, 'store']);
    Route::get('field-report/{id}', [FieldReportController::class, 'show']);
    Route::put('field-report/{id}', [FieldReportController::class, 'update']);
    Route::delete('field-report/{id}', [FieldReportController::class, 'destroy']);
    /*field report route end*/

    /*volunteer task route start*/
    Route::get('volunteer-task', [VolunteerTaskController::class, 'index']);
    Route::post('volunteer-task', [VolunteerTaskController::class, 'store']);
    Route::get('volunteer-task/{id}', [VolunteerTaskController::class, 'show']);
    Route::put('volunteer-task/{id}', [VolunteerTaskController::class, 'update']);
    Route::patch('volunteer-task/change-status/{id}', [VolunteerTaskController::class, 'changeStatus']);
    Route::delete('volunteer-task/{id}', [VolunteerTaskController::class, 'destroy']);
    /*volunteer task route end*/

    /*volunteer area assignments route start*/
    Route::get('volunteer-area-assignment', [VolunteerAreaAssignmentController::class, 'index']);
    Route::post('volunteer-area-assignment', [VolunteerAreaAssignmentController::class, 'store']);
    Route::get('volunteer-area-assignment/{id}', [VolunteerAreaAssignmentController::class, 'show']);
    Route::put('volunteer-area-assignment/{id}', [VolunteerAreaAssignmentController::class, 'update']);
    Route::delete('volunteer-area-assignment/{id}', [VolunteerAreaAssignmentController::class, 'destroy']);
    /*volunteer area assignments route end*/

    /*volunteer route start*/
    Route::get('volunteer', [volunteerController::class, 'index']);
    Route::post('volunteer', [volunteerController::class, 'store']);
    Route::get('volunteer/{id}', [volunteerController::class, 'show']);
    Route::put('volunteer/{id}', [volunteerController::class, 'update']);
    Route::delete('volunteer/{id}', [volunteerController::class, 'destroy']);
    /*volunteer route end*/

    /*service application route start*/
    Route::get('service-applicant', [ServiceApplicantController::class, 'index']);
    Route::post('service-applicant', [ServiceApplicantController::class, 'store']);
    Route::get('service-applicant/{id}', [ServiceApplicantController::class, 'show']);
    Route::put('service-applicant/{id}', [ServiceApplicantController::class, 'update']);
    Route::delete('service-applicant/{id}', [ServiceApplicantController::class, 'destroy']);
    Route::put('service-applicant/status-update/{id}', [ServiceApplicantController::class, 'changeStatus']);
    /*service application route end*/

    /*ward commissioner route start*/
    Route::get('ward-commissioner', [WardCommissionerController::class, 'index']);
    Route::post('ward-commissioner', [WardCommissionerController::class, 'store']);
    Route::get('ward-commissioner/{id}', [WardCommissionerController::class, 'show']);
    Route::put('ward-commissioner/{id}', [WardCommissionerController::class, 'update']);
    Route::delete('ward-commissioner/{id}', [WardCommissionerController::class, 'destroy']);
    Route::patch('ward-commissioner/status-update/{id}', [WardCommissionerController::class, 'changeStatus']);
    /*ward commissioner route end*/

    /*area demographic route start*/
    Route::get('area-demographic', [AreaDemographicController::class, 'index']);
    Route::post('area-demographic', [AreaDemographicController::class, 'store']);
    Route::get('area-demographic/{id}', [AreaDemographicController::class, 'show']);
    Route::put('area-demographic/{id}', [AreaDemographicController::class, 'update']);
    Route::delete('area-demographic/{id}', [AreaDemographicController::class, 'destroy']);
    /*area demographic route end*/

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