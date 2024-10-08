<?php

declare(strict_types=1);


namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepository;
use InvalidArgumentException;

readonly class RegistrationService
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function register(array $data): User
    {
        if ($this->userRepository->findByEmail($data['email']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким email уже зарегистрирован.');
        }

        $user = User::create(
            $data['name'],
            $data['email'],
            $data['password'],
        );

        $this->userRepository->save($user);

        return $user;
    }
}
