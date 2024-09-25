<?php

declare(strict_types=1);


namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{

    public function save(User $user): bool
    {
        return $user->save();
    }

    public function delete(int $id): bool
    {
        return User::destroy($id)>0;
    }

    public function findById(int $id): ?User
    {
        return User::query()->find($id);
    }

    public function all(): Collection
    {
        return User::all();
    }

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }

    public function filterByName(string $name): Collection
    {
        return User::query()->where('name', $name)->get();
    }
}
