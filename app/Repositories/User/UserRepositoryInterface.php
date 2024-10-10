<?php

declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface
{
    public function save(User $user): bool;

    public function delete(int $id): bool;

    public function findById(int $id): ?User;

    /**
     * @return Collection<int, User>
     */
    public function all(): Collection;

    public function findByEmail(string $email): ?User;

    /**
     * @return Collection<int, User>
     */
    public function filterByName(string $name): Collection;
}
