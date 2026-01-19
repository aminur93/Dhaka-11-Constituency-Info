<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\EventRegistrationRequest;
use App\Http\Services\Api\V1\Admin\EventRegistration\EventRegistrationService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventRegistrationController extends Controller
{
    protected EventRegistrationService $eventRegistrationService;

    public function __construct(EventRegistrationService $eventRegistrationService)
    {
        $this->eventRegistrationService = $eventRegistrationService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch event registration via service
        $event_registration = $pagination
            ? $this->eventRegistrationService->index($request)
            : $this->eventRegistrationService->getAllEventRegistration();



        // Return unified response
        $message = $pagination
            ? "All event registration fetched successfully with pagination"
            : "All event registration fetched successfully";

        return GlobalResponse::success($event_registration, $message, Response::HTTP_OK);
    }

    public function store(EventRegistrationRequest $request)
    {
        try {
           $event_registration = $this->eventRegistrationService->store($request);

           return GlobalResponse::success($event_registration, "Event Registration Store Successful", Response::HTTP_CREATED);

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
            $event_registration = $this->eventRegistrationService->show($id);

            return GlobalResponse::success($event_registration, "Event Registration fetched successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Event Registration not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(EventRegistrationRequest $request, int $id)
    {
        try {
            $event_registration = $this->eventRegistrationService->update($request, $id);

            return GlobalResponse::success($event_registration, "Event Registration updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Event Registration not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

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
            $this->eventRegistrationService->destroy($id);

            return GlobalResponse::success(null, "Event Registration deleted successfully", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Event Registration not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        try {
            $event_registration = $this->eventRegistrationService->changeStatus($request, $id);

            return GlobalResponse::success($event_registration, "Event Registration status updated successfully", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Event Registration not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}