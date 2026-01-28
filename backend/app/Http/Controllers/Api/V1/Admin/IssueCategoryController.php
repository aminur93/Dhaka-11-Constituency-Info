<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\IssueCategoryRequest;
use App\Http\Services\Api\V1\Admin\IssueCategory\IssueCategoryService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IssueCategoryController extends Controller
{
    protected IssueCategoryService $issueCategoryService;

    public function __construct(IssueCategoryService $issueCategoryService)
    {
        $this->issueCategoryService = $issueCategoryService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch issue category via service
        $issueCategory = $pagination
            ? $this->issueCategoryService->index($request)
            : $this->issueCategoryService->getAllIssueCategories();



        // Return unified response
        $message = $pagination
            ? "All issue category fetched successfully with pagination"
            : "All issue category fetched successfully";

        return GlobalResponse::success($issueCategory, $message, Response::HTTP_OK);
    }

    public function store(IssueCategoryRequest $request)
    {
        try {
           $issueCategory = $this->issueCategoryService->store($request);

           return GlobalResponse::success($issueCategory, "Issue Category Store successful", Response::HTTP_CREATED);

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
            $issueCategory = $this->issueCategoryService->show($id);

            return GlobalResponse::success($issueCategory, "Issue Category fetched successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Issue Category not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $issueCategory = $this->issueCategoryService->update($request, $id);

            return GlobalResponse::success($issueCategory, "Issue Category updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Issue Category not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
        
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
            $this->issueCategoryService->destroy($id);

            return GlobalResponse::success(null, "Issue Category deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Issue Category not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(int $id)
    {
        try {
            $issueCategory = $this->issueCategoryService->changeStatus($id);

            return GlobalResponse::success($issueCategory, "Issue Category status updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Issue Category not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}