<?php

declare(strict_types=1);

namespace App\Services\User;

use App\DTO\User\RegisterDTO;
use App\Models\User;
use App\Repositories\User\UserRepository;
use InvalidArgumentException;

readonly class RegistrationService
{
    public function __construct(private UserRepository $userRepository) {}

    public function register(RegisterDTO $registerDTO): User
    {
        if ($this->userRepository->findByEmail($registerDTO->email) !== null) {
            throw new InvalidArgumentException('The user with the email is already registered.');
        }

        $user = User::create(
            $registerDTO->name,
            $registerDTO->email,
            $registerDTO->password,
        );

        $this->userRepository->save($user);

        return $user;
    }
}
