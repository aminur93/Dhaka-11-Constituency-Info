<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\NoticeRequest;
use App\Http\Services\Api\V1\Admin\Notice\NoticeService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NoticeController extends Controller
{
    protected NoticeService $noticeService;

    public function __construct(NoticeService $noticeService)
    {
        $this->noticeService = $noticeService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch notice via service
        $notice = $pagination
            ? $this->noticeService->index($request)
            : $this->noticeService->getAllNotices();



        // Return unified response
        $message = $pagination
            ? "All notice fetched successfully with pagination"
            : "All notice fetched successfully";

        return GlobalResponse::success($notice, $message, Response::HTTP_OK);
    }

    public function store(NoticeRequest $request)
    {
        try {
           $notice = $this->noticeService->store($request);

           return GlobalResponse::success($notice, "Notice Store successful", Response::HTTP_CREATED);

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
            $notice = $this->noticeService->show($id);

            return GlobalResponse::success($notice, "Notice fetched successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Notice not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $notice = $this->noticeService->update($request, $id);

            return GlobalResponse::success($notice, "Notice updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Notice not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

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
            $this->noticeService->destroy($id);

            return GlobalResponse::success(null, "Notice deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Notice not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}