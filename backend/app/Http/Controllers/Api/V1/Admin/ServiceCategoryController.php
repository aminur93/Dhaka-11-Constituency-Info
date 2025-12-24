<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\ServiceCategoryRequest;
use App\Http\Services\Api\V1\Admin\ServiceCategory\ServiceCategoryService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ServiceCategoryController extends Controller
{
    protected ServiceCategoryService $serviceCategoryService;

    public function __construct(ServiceCategoryService $serviceCategoryService)
    {
        $this->serviceCategoryService = $serviceCategoryService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch permissions via service
        $serviceCategory = $pagination
            ? $this->serviceCategoryService->index($request)
            : $this->serviceCategoryService->getAllServiceCategories();



        // Return unified response
        $message = $pagination
            ? "All hero fetched successfully with pagination"
            : "All hero fetched successfully";

        return GlobalResponse::success($serviceCategory, $message, Response::HTTP_OK);
    }

    public function store(ServiceCategoryRequest $request)
    {
        try {
           $serviceCategory = $this->serviceCategoryService->store($request);

           return GlobalResponse::success($serviceCategory, "Service Category Store successful", Response::HTTP_CREATED);

        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        try {
            $serviceCategory = $this->serviceCategoryService->show($id);

            return GlobalResponse::success($serviceCategory, "Service Category fetched successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Service Category not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $serviceCategory = $this->serviceCategoryService->update($request, $id);

            return GlobalResponse::success($serviceCategory, "Service Category updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Service Category not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (ValidationException $exception) {

            return GlobalResponse::error($exception->errors(), $exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

      public function destroy(int $id)
    {
        try {
            $this->serviceCategoryService->destroy($id);

            return GlobalResponse::success(null, "Service Category deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Service Category not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function statusUpdate(int $id)
    {
        try {
            $serviceCategory = $this->serviceCategoryService->statusUpdate($id);

            return GlobalResponse::success($serviceCategory, "Service Category status updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Service Category not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}