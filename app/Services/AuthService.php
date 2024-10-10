<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function getAuthenticatedUser(): User
    {
        $authenticatedUser = Auth::user();

        if (! $authenticatedUser instanceof User) {
            throw new \RuntimeException('User is not authenticated.');
        }

        return $authenticatedUser;
    }
}
