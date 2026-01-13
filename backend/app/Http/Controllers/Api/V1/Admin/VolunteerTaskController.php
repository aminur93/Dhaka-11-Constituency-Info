<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Helper\GlobalResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Admin\VolunteerTaskRequest;
use App\Http\Services\Api\V1\Admin\VolunteerTask\VolunteerTaskService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VolunteerTaskController extends Controller
{
    protected VolunteerTaskService $volunteerTaskService;

    public function __construct(VolunteerTaskService $volunteerTaskService)
    {
        $this->volunteerTaskService = $volunteerTaskService;
    }

    public function index(Request $request)
    {
        //Convert pagination query to boolean
        $pagination = filter_var($request->get('pagination', true), FILTER_VALIDATE_BOOLEAN);

        // Fetch volunteer task via service
        $volunteer_tasks = $pagination
            ? $this->volunteerTaskService->index($request)
            : $this->volunteerTaskService->getAllVolunteerTasks();


        // Return unified response
        $message = $pagination
            ? "All volunteer task fetched successfully with pagination"
            : "All volunteer task fetched successfully";

        return GlobalResponse::success($volunteer_tasks, $message, Response::HTTP_OK);
    }

    public function store(VolunteerTaskRequest $request)
    {
        try {
           $volunteer_task = $this->volunteerTaskService->store($request);

           return GlobalResponse::success($volunteer_task, "Volunteer task Store successful", Response::HTTP_CREATED);

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

            $volunteer_task = $this->volunteerTaskService->show($id);

            return GlobalResponse::success($volunteer_task, "Volunteer task fetch successful", \Illuminate\Http\Response::HTTP_OK);

        }catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Volunteer task not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);
            
        }catch (\Exception $exception){

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

     public function update(VolunteerTaskRequest $request, $id)
    {
        try {

            $volunteer_task = $this->volunteerTaskService->update($request, $id);

            return GlobalResponse::success($volunteer_task, "Volunteer task update successful", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Volunteer task not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

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

            $this->volunteerTaskService->destroy($id);

            return GlobalResponse::success("", "Volunteer task delete successful", Response::HTTP_NO_CONTENT);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Volunteer task not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        try {

            $volunteer_task = $this->volunteerTaskService->changeStatus($request, $id);

            return GlobalResponse::success($volunteer_task, "Volunteer task status update successful", Response::HTTP_OK);

        } catch (ModelNotFoundException $exception){

            return GlobalResponse::error("Volunteer task not found.", $exception->getMessage(), Response::HTTP_NOT_FOUND);

        } catch (HttpException $exception) {

            return GlobalResponse::error("", $exception->getMessage(), $exception->getStatusCode());

        } catch (Exception $exception) {

            return GlobalResponse::error("", $exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}