<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Services\User\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct(private readonly AuthenticationService $authenticationService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $token = $this->authenticationService->login($request->validated());

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authenticationService->logout($request->user());

        return response()->json(['message' => 'Logged out']);
    }
}
