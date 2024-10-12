<?php

declare(strict_types=1);

namespace App\DTO\User;

class RegisterDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}
}
