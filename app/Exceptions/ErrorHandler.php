<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class ErrorHandler extends ExceptionHandler implements AuthenticatesRequests
{
    public function report(Throwable $e): void
    {
        if (! $e instanceof InvalidArgumentException) {
            parent::report($e);
        }
    }

    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return $this->jsonResponse(401, 'Unauthorized');
    }

    public function render($request, Throwable $e): JsonResponse
    {
        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if ($e instanceof HttpException) {
            return $this->jsonResponse($e->getStatusCode(), $e->getMessage());
        }

        if ($e instanceof InvalidArgumentException) {
            return $this->jsonResponse(400, $e->getMessage());
        }

        //        return $this->jsonResponse(500, 'Internal Server Error');
        return $this->jsonResponse(500, $e->getMessage());
    }

    private function jsonResponse(int $code, string $message): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
        ], $code);
    }
}
