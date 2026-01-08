<?php

namespace App\Providers;

use App\Http\Services\Api\V1\Admin\AreaDemographic\AreaDemographicService;
use App\Http\Services\Api\V1\Admin\AreaDemographic\AreaDemographicServiceImpl;
use App\Http\Services\Api\V1\Admin\Disaster\DisasterService;
use App\Http\Services\Api\V1\Admin\Disaster\DisasterServiceImpl;
use App\Http\Services\Api\V1\Admin\District\DistrictService;
use App\Http\Services\Api\V1\Admin\District\DistrictServiceImpl;
use App\Http\Services\Api\V1\Admin\Division\DivisionService;
use App\Http\Services\Api\V1\Admin\Division\DivisionServiceImpl;
use App\Http\Services\Api\V1\Admin\Education\EducationService;
use App\Http\Services\Api\V1\Admin\Education\EducationServiceImpl;
use App\Http\Services\Api\V1\Admin\Financial\FinancialService;
use App\Http\Services\Api\V1\Admin\Financial\FinancialServiceImpl;
use App\Http\Services\Api\V1\Admin\Hero\HeroService;
use App\Http\Services\Api\V1\Admin\Hero\HeroServiceImpl;
use App\Http\Services\Api\V1\Admin\Job\JobService;
use App\Http\Services\Api\V1\Admin\Job\JobServiceImpl;
use App\Http\Services\Api\V1\Admin\LegalAid\LegalAidService;
use App\Http\Services\Api\V1\Admin\LegalAid\LegalAidServiceImpl;
use App\Http\Services\Api\V1\Admin\LogoBannerSlider\LogoBannerSlideService;
use App\Http\Services\Api\V1\Admin\LogoBannerSlider\LogoBannerSlideServiceImpl;
use App\Http\Services\Api\V1\Admin\Medical\MedicalService;
use App\Http\Services\Api\V1\Admin\Medical\MedicalServiceImpl;
use App\Http\Services\Api\V1\Admin\Permission\PermissionService;
use App\Http\Services\Api\V1\Admin\Permission\PermissionServiceImpl;
use App\Http\Services\Api\V1\Admin\Role\RoleService;
use App\Http\Services\Api\V1\Admin\Role\RoleServiceImpl;
use App\Http\Services\Api\V1\Admin\ServiceApplicant\ServiceApplicantService;
use App\Http\Services\Api\V1\Admin\ServiceApplicant\ServiceApplicantServiceImpl;
use App\Http\Services\Api\V1\Admin\ServiceCategory\ServiceCategoryService;
use App\Http\Services\Api\V1\Admin\ServiceCategory\ServiceCategoryServiceImpl;
use App\Http\Services\Api\V1\Admin\Thana\ThanaService;
use App\Http\Services\Api\V1\Admin\Thana\ThanaServiceImpl;
use App\Http\Services\Api\V1\Admin\Union\UnionService;
use App\Http\Services\Api\V1\Admin\Union\UnionServiceImpl;
use App\Http\Services\Api\V1\Admin\User\UserService;
use App\Http\Services\Api\V1\Admin\User\UserServiceImpl;
use App\Http\Services\Api\V1\Admin\Ward\WardService;
use App\Http\Services\Api\V1\Admin\Ward\WardServiceImpl;
use App\Http\Services\Api\V1\Admin\WardCommissioner\WardCommissionerService;
use App\Http\Services\Api\V1\Admin\WardCommissioner\WardCommissionerServiceImpl;
use App\Http\Services\Api\V1\Auth\AuthService;
use App\Http\Services\Api\V1\Auth\AuthServiceImpl;
use Illuminate\Support\ServiceProvider;

class ServiceBindingProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthService::class,
            AuthServiceImpl::class
        );

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

        $this->app->bind(
            DivisionService::class,
            DivisionServiceImpl::class
        );

        $this->app->bind(
            DistrictService::class,
            DistrictServiceImpl::class
        );

        $this->app->bind(
            ThanaService::class,
            ThanaServiceImpl::class
        );

        $this->app->bind(
            UnionService::class,
            UnionServiceImpl::class
        );

        $this->app->bind(
            WardService::class,
            WardServiceImpl::class
        );

        $this->app->bind(
            AreaDemographicService::class,
            AreaDemographicServiceImpl::class
        );

        $this->app->bind(
            WardCommissionerService::class,
            WardCommissionerServiceImpl::class
        );

        $this->app->bind(
            MedicalService::class,
            MedicalServiceImpl::class
        );

        $this->app->bind(
            FinancialService::class,
            FinancialServiceImpl::class
        );

        $this->app->bind(
            EducationService::class,
            EducationServiceImpl::class
        );

        $this->app->bind(
            JobService::class,
            JobServiceImpl::class
        );

        $this->app->bind(
            LegalAidService::class,
            LegalAidServiceImpl::class
        );

        $this->app->bind(
            DisasterService::class,
            DisasterServiceImpl::class
        );

        $this->app->bind(
            ServiceApplicantService::class,
            ServiceApplicantServiceImpl::class
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