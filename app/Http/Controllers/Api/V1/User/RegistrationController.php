<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Services\User\RegistrationService;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(private readonly RegistrationService $registrationService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->registrationService->register($request->validated());

        return response()->json(['user' => $user], 201);
    }
}
