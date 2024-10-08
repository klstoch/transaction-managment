<?php

declare(strict_types=1);

namespace App\Repositories\Transaction;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function save(Transaction $transaction): bool
    {
        if ($transaction->save()) {
            $transaction->refresh();
            return true;
        }
        return false;
    }

    public function find(int $id): ?Transaction
    {
        return Transaction::query()->find($id);
    }

    public function delete(int $id): bool
    {
        return Transaction::destroy($id) > 0;
    }

    public function filterBy(array $filters): Collection
    {
        $query = Transaction::query();

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->get();
    }

    public function all(): Collection
    {
        return Transaction::all();
    }
}
