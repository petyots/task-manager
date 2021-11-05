<?php

namespace App\Domain\Tasks\Tests\Unit;

use App\Domain\Tasks\Http\Controllers\TasksController;
use App\Domain\Tasks\Http\Requests\CreateTaskRequest;
use App\Infrastructure\Support\ExceptionFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class TasksControllerTest extends TestCase
{
    public function testCreateTaskHandleCanHandleException()
    {
        $controller = $this->createMock(TasksController::class);

        $response = new JsonResponse([
            'message' => 'Something went wrong..',
            'status_code' => 500,
        ], 500);

        $request = CreateTaskRequest::create('/', 'post', ['name' => null]);

        $controller->method('store')
            ->with($request)
            ->willReturn($response);

        $this->assertEquals($response, $controller->store($request));
    }
}
