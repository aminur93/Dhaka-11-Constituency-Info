<?php

namespace App\Providers;

use App\Http\Services\Api\V1\Admin\Hero\HeroService;
use App\Http\Services\Api\V1\Admin\Hero\HeroServiceImpl;
use App\Http\Services\Api\V1\Admin\LogoBannerSlider\LogoBannerSlideService;
use App\Http\Services\Api\V1\Admin\LogoBannerSlider\LogoBannerSlideServiceImpl;
use App\Http\Services\Api\V1\Admin\Permission\PermissionService;
use App\Http\Services\Api\V1\Admin\Permission\PermissionServiceImpl;
use App\Http\Services\Api\V1\Admin\Role\RoleService;
use App\Http\Services\Api\V1\Admin\Role\RoleServiceImpl;
use App\Http\Services\Api\V1\Admin\ServiceCategory\ServiceCategoryService;
use App\Http\Services\Api\V1\Admin\ServiceCategory\ServiceCategoryServiceImpl;
use App\Http\Services\Api\V1\Admin\User\UserService;
use App\Http\Services\Api\V1\Admin\User\UserServiceImpl;
use Illuminate\Support\ServiceProvider;

class ServiceBindingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            PermissionService::class,
            PermissionServiceImpl::class
        );

        $this->app->bind(
            RoleService::class,
            RoleServiceImpl::class
        );

        $this->app->bind(
            UserService::class,
            UserServiceImpl::class
        );

        $this->app->bind(
            LogoBannerSlideService::class,
            LogoBannerSlideServiceImpl::class
        );

        $this->app->bind(
            HeroService::class,
            HeroServiceImpl::class
        );

        $this->app->bind(
            ServiceCategoryService::class,
            ServiceCategoryServiceImpl::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}