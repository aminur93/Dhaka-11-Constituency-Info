<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Services\Api\V1\Admin\LogoBannerSlider\LogoBannerSlideService;
use App\Models\LogoBannerSlide;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationData;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class LogoBannerSlideController extends Controller
{
    protected LogoBannerSlideService $logoBannerSlideService;

    public function __construct(LogoBannerSlideService $logoBannerSlideService)
    {
        $this->logoBannerSlideService = $logoBannerSlideService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch permissions via service
        $logoBannerSlider = $pagination
            ? $this->logoBannerSlideService->index($request)
            : $this->logoBannerSlideService->getAllLogoBannerSlides();



        // Return unified response
        $message = $pagination
            ? "All logo banner slides fetched successfully with pagination"
            : "All logo banner slides fetched successfully";

        return GlobalResponse::success($logoBannerSlider, $message, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        try {
           $logoBannerSlider = $this->logoBannerSlideService->store($request);

           return GlobalResponse::success($logoBannerSlider, "Logo Banner Slider Store successful", Response::HTTP_CREATED);

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
            $logoBannerSlider = $this->logoBannerSlideService->show($id);

            return GlobalResponse::success($logoBannerSlider, "Logo Banner Slider fetched successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Permission not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, int $id)
    {
        try {
            $logoBannerSlider = $this->logoBannerSlideService->update($request, $id);

            return GlobalResponse::success($logoBannerSlider, "Logo Banner Slider updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Logo Banner Slider not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

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
            $this->logoBannerSlideService->destroy($id);

            return GlobalResponse::success(null, "Logo Banner Slider deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Logo Banner Slider not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function statusUpdate(int $id)
    {
        try {
            $logoBannerSlider = $this->logoBannerSlideService->statusUpdate($id);

            return GlobalResponse::success($logoBannerSlider, "Logo Banner Slider status updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Logo Banner Slider not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}