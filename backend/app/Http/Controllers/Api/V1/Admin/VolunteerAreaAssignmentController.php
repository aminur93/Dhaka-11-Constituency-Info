<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\VolunteerAreaAssignmentRequest;
use App\Http\Services\Api\V1\Admin\VolunteerAreaAssignment\VolunteerAreaAssignmentService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VolunteerAreaAssignmentController extends Controller
{
    protected VolunteerAreaAssignmentService $volunteerAreaAssignmentService;

    public function __construct(VolunteerAreaAssignmentService $volunteerAreaAssignmentService)
    {
        $this->volunteerAreaAssignmentService = $volunteerAreaAssignmentService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch volunteer area assignment via service
        $volunteer_area_assignment = $pagination
            ? $this->volunteerAreaAssignmentService->index($request)
            : $this->volunteerAreaAssignmentService->getAllVolunteerAreaAssignments();


        // Return unified response
        $message = $pagination
            ? "All volunteer area assignment fetched successfully with pagination"
            : "All volunteer area assignment fetched successfully";

        return GlobalResponse::success($volunteer_area_assignment, $message, Response::HTTP_OK);
    }

    public function store(VolunteerAreaAssignmentRequest $request)
    {
        try {
           $volunteer_area_assignment = $this->volunteerAreaAssignmentService->store($request);

           return GlobalResponse::success($volunteer_area_assignment, "Volunteer area assignment Store successful", Response::HTTP_CREATED);

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

            $volunteer_area_assignment = $this->volunteerAreaAssignmentService->show($id);

            return GlobalResponse::success($volunteer_area_assignment, "Volunteer area assignment fetch successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Volunteer area assignment not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(VolunteerAreaAssignmentRequest $request, $id)
    {
        try {

            $volunteer = $this->volunteerAreaAssignmentService->update($request, $id);

            return GlobalResponse::success($volunteer, "Volunteer area assignment update successful", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Volunteer area assignment not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

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

            $this->volunteerAreaAssignmentService->destroy($id);

            return GlobalResponse::success("", "Volunteer area assignment delete successful", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Volunteer area assignment not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}