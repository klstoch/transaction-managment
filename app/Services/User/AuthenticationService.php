<?php

declare(strict_types=1);


namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepository;
use PHPUnit\Event\InvalidArgumentException;


readonly class AuthenticationService
{
    public function __construct(private  UserRepository $userRepository)
    {
    }

    public function login(array $data): string
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if (!$user) {
            throw new InvalidArgumentException('Неправильные данные для входа.');
        }

        $user->checkPassword($data['password']);

        return $user->createToken('auth_token')->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
