<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\User;
use App\Repositories\User\UserRepository;
use InvalidArgumentException;

readonly class RegistrationService
{
    public function __construct(private UserRepository $userRepository) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function register(array $data): User
    {
        if ($this->userRepository->findByEmail($data['email']) !== null) {
            throw new InvalidArgumentException('The user with the email is already registered.');
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
