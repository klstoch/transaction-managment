<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ErrorHandler extends ExceptionHandler
{

    public function report(Throwable $e): void
    {
        if (!$e instanceof InvalidArgumentException) {
            parent::report($e);
        }
    }

    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
            $errorMessage = $e->getMessage();
        } elseif ($e instanceof InvalidArgumentException) {
            $statusCode = 400;
            $errorMessage = $e->getMessage();
        } else {
            $statusCode = 500;
            $errorMessage = 'Internal Server Error';
        }

        return response()->json([
            'status' => 'error',
            'code' => $statusCode,
            'message' => $errorMessage,
        ], $statusCode);
    }
}
