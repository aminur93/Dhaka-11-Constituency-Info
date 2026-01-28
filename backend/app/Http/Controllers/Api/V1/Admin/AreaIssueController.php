<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Api\V1\Admin\AreaIssue\AreaIssueService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AreaIssueController extends Controller
{
    protected AreaIssueService $areaIssueService;

    public function __construct(AreaIssueService $areaIssueService)
    {
        $this->areaIssueService = $areaIssueService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch area issues via service
        $area_issues = $pagination
            ? $this->areaIssueService->index($request)
            : $this->areaIssueService->getAllAreaIssues();


        // Return unified response
        $message = $pagination
            ? "All area issues fetched successfully with pagination"
            : "All area issues fetched successfully";

        return GlobalResponse::success($area_issues, $message, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
           $areaIssue = $this->areaIssueService->store($request);

           return GlobalResponse::success($areaIssue, "Area Issue Store successful", Response::HTTP_CREATED);

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

            $areaIssue = $this->areaIssueService->show($id);

            return GlobalResponse::success($areaIssue, "Area Issue fetch successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Area Issue not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id)
    {
        try {

            $areaIssue = $this->areaIssueService->update($request, $id);
            
            return GlobalResponse::success($areaIssue, "Area Issue update successful", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Area Issue not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

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

            $this->areaIssueService->destroy($id);

            return GlobalResponse::success("", "Area Issue deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Area Issue not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {

            $this->areaIssueService->bulkDestroy($request);

            return GlobalResponse::success("", "Area Issues deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        try {

            $areaIssue = $this->areaIssueService->changeStatus($request, $id);

            return GlobalResponse::success($areaIssue, "Area Issue status change successful", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception) {

            return GlobalResponse::error("Area Issue not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}