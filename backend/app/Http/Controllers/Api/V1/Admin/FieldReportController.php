<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\FieldReportRequest;
use App\Http\Services\Api\V1\Admin\FieldReport\FieldReportService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FieldReportController extends Controller
{
    protected FieldReportService $fieldReportService;

    public function __construct(FieldReportService $fieldReportService)
    {
        $this->fieldReportService = $fieldReportService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch permissions via service
        $field_reports = $pagination
            ? $this->fieldReportService->index($request)
            : $this->fieldReportService->getAllFieldReports();



        // Return unified response
        $message = $pagination
            ? "All field report fetched successfully with pagination"
            : "All field report fetched successfully";

        return GlobalResponse::success($field_reports, $message, Response::HTTP_OK);
    }

    public function store(FieldReportRequest $request)
    {
        try {
           $field_report = $this->fieldReportService->store($request);

           return GlobalResponse::success($field_report, "Field report Store successful", Response::HTTP_CREATED);

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
            $field_report = $this->fieldReportService->show($id);

            return GlobalResponse::success($field_report, "Field report fetched successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Field report not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $field_report = $this->fieldReportService->update($request, $id);

            return GlobalResponse::success($field_report, "Field report updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Field report not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

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
            $this->fieldReportService->destroy($id);

            return GlobalResponse::success(null, "Field report deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Field report not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}