<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Http\Resources\ApiErrorResponseResource;
use App\Infrastructure\Support\ExceptionFormat;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ResponseTrait
{
    /**
     * The current path of resource to respond
     *
     * @var string
     */
    protected string $resourceItem;

    /**
     * The current path of collection resource to respond
     *
     * @var string
     */
    protected string $resourceCollection;

    protected function respondWithCustomData($data, string $message = null, $status = 200): JsonResponse
    {
        return new JsonResponse([
            'message' => $message,
            'data' => $data,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ], $status);
    }

    protected function getTimestampInMilliseconds(): int
    {
        return intdiv((int)now()->timezone('Europe/Sofia')->format('Uu'), 1000);
    }

    /**
     * Return no content for delete requests
     */
    protected function respondWithNoContent(): JsonResponse
    {
        return new JsonResponse([
            'data' => null,
            'meta' => ['timestamp' => $this->getTimestampInMilliseconds()],
        ], Response::HTTP_NO_CONTENT);
    }

    /**
     * Return collection response from the application
     */
    protected function respondWithCollection(LengthAwarePaginator $collection)
    {
        return (new $this->resourceCollection($collection))->additional(
            ['meta' => ['timestamp' => $this->getTimestampInMilliseconds()]]
        );
    }

    /**
     * Return single item response from the application
     */
    protected function respondWithItem(Model $item)
    {
        return (new $this->resourceItem($item))->additional(
            ['meta' => ['timestamp' => $this->getTimestampInMilliseconds()]]
        );
    }

    protected function respondWithError(
        string $message = 'Something went wrong. Please try again later',
        int $statusCode = 500,
        ?string $developerMessage = null,
        ?string $errorCode = null,
        \Exception | \Throwable | \Error | null $exception = null,
    ): JsonResponse {
        $data = [
            'message' => __($message),
            'status_code' => $statusCode,
            'developer_message' => config('app.env') !== 'production' ? $developerMessage : null,
            'error_code' => $errorCode,
            'exception' => ! is_null($exception) && config('app.env') !== 'production' ?
                ExceptionFormat::toArray($exception) :
                null,
        ];

        ApiErrorResponseResource::withoutWrapping();

        $data = ApiErrorResponseResource::make($data)
            ->additional(
                ['meta' => ['timestamp' => $this->getTimestampInMilliseconds()]]
            )
            ->toArray(request());

        return response()->json($data, $statusCode, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
