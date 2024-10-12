<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DTO\User\LoginDTO;
use App\Models\User;
use App\Repositories\User\UserRepository;
use PHPUnit\Event\InvalidArgumentException;

readonly class AuthenticationService
{
    public function __construct(private UserRepository $userRepository) {}

    public function login(LoginDTO $loginDTO): string
    {
        $user = $this->userRepository->findByEmail($loginDTO->email);

        if (! $user) {
            throw new InvalidArgumentException('Invalid login information');
        }

        $user->checkPassword($loginDTO->password);

        return $user->createToken('auth_token')->plainTextToken;
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
