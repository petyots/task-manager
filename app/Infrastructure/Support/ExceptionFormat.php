<?php

namespace App\Infrastructure\Support;

use Exception;
use Illuminate\Support\Arr;

final class ExceptionFormat
{
    public static function log(Exception $exception)
    {
        $message = 'File:'.$exception->getFile().PHP_EOL;
        $message .= 'Line:'.$exception->getLine().PHP_EOL;
        $message .= 'Message:'.$exception->getMessage().PHP_EOL;
        $message .= 'Stacktrace:'.PHP_EOL;
        $message .= $exception->getTraceAsString();

        return $message;
    }

    public static function toArray(Exception | \TypeError | \Error  | \Throwable $exception): array
    {
        return [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'stacktrace' => collect($exception->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
        ];
    }
}
