<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\DTO\User\RegisterDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Services\User\RegistrationService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;

class RegistrationController extends Controller
{
    public function __construct(private readonly RegistrationService $registrationService) {}

    public function register(RegisterRequest $request): JsonResponse
    {

        $name = $request->validated('name');
        $email = $request->validated('email');
        $password = $request->validated('password');

        if (! is_string($name) || ! is_string($email) || ! is_string($password)) {
            throw new InvalidArgumentException('Неверные данные: имя, email и пароль должны быть строками.');
        }

        $dto = new RegisterDTO($name, $email, $password);

        $user = $this->registrationService->register($dto);

        return response()->json(['user' => $user], 201);
    }
}
