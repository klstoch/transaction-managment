<?php

declare(strict_types=1);

namespace App\DTO\User;

class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
