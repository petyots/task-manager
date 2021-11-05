<?php

namespace App\Domain\Tasks\Http\Controllers;

use App\Domain\Tasks\Actions\CreateOrUpdateTaskAction;
use App\Domain\Tasks\Events\NewTaskCreated;
use App\Domain\Tasks\Events\TaskUpdated;
use App\Domain\Tasks\Factories\TaskDataFactory;
use App\Domain\Tasks\Http\Requests\ChangeTaskStatusRequests;
use App\Domain\Tasks\Http\Requests\CreateTaskRequest;
use App\Domain\Tasks\Http\Resources\TaskResource;
use App\Domain\Tasks\Http\Resources\TaskResourceCollection;
use App\Domain\Tasks\Models\Task;
use App\Interfaces\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TasksController extends Controller
{
    public function __construct(private CreateOrUpdateTaskAction $createOrUpdateTaskAction)
    {
        $this->resourceItem = TaskResource::class;
        $this->resourceCollection = TaskResourceCollection::class;
    }

    public function store(CreateTaskRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = TaskDataFactory::fromCreateTaskRequest($request);

            $task = $this->createOrUpdateTaskAction->execute($data);

            DB::afterCommit(function () use ($task) {
                event(new NewTaskCreated($task));
            });

            DB::commit();

            return $this->respondWithCustomData(
                data: ['uuid' => $data->uuid],
                message: __('Task created.'),
                status: Response::HTTP_CREATED
            );
            /** @codeCoverageIgnoreStart  */
        } catch (\Throwable $exception) {
            DB::rollBack();

            report($exception);

            return $this->respondWithError(exception: $exception);
        }
        /** @codeCoverageIgnoreEnd  */
    }

    public function changeStatus(ChangeTaskStatusRequests $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = TaskDataFactory::updateTaskDataFromChangeStatsRequest($request);

            $task = $this->createOrUpdateTaskAction->execute($data);

            DB::afterCommit(function () use ($task) {
                event(new TaskUpdated($task));
            });

            DB::commit();

            return $this->respondWithCustomData(data: [], message: __('Task status updated successfully.'));
            /** @codeCoverageIgnoreStart  */
        } catch (\Throwable $exception) {
            DB::rollBack();

            report($exception);

            return $this->respondWithError(exception: $exception);
        }
        /** @codeCoverageIgnoreEnd  */

    }

    public function index(Request $request): TaskResourceCollection|JsonResponse
    {
        try {
            $data = Task::query()
                ->where('user_id', '=', auth()->id())
                ->paginate($request->get('perPage', 20));

            return $this->respondWithCollection($data);
            /** @codeCoverageIgnoreStart  */
        } catch (\Throwable $exception) {
            report($exception);

            return $this->respondWithError(exception: $exception);
        }
        /** @codeCoverageIgnoreEnd  */
    }
}
