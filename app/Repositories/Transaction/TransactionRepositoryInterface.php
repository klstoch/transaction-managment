<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function save(Transaction $transaction): bool;

    public function find(int $id): ?Transaction;

    public function delete(int $id): bool;

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, Transaction>
     */
    public function filterBy(array $filters): Collection;

    /**
     * @return Collection<int, Transaction>
     */
    public function all(): Collection;
}
