<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\DTO\User\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Models\User;
use App\Services\User\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticationController extends Controller
{
    public function __construct(private readonly AuthenticationService $authenticationService) {}

    public function login(LoginRequest $request): JsonResponse
    {

        $email = $request->validated('email');
        $password = $request->validated('password');

        if (! is_string($email) || ! is_string($password)) {
            throw new InvalidArgumentException('Неверные данные: email и пароль должны быть строками.');
        }

        $dto = new LoginDTO($email, $password);

        $token = $this->authenticationService->login($dto);

        return response()->json(['token' => $token]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            throw new UnauthorizedHttpException('Unauthorized', 'Пользователь не авторизован.');
        }
        $this->authenticationService->logout($user);

        return response()->json(['message' => 'Logged out']);
    }
}
