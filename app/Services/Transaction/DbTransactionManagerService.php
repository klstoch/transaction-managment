<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use Illuminate\Support\Facades\DB;

class DbTransactionManagerService
{
    public function begin(): void
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollback(): void
    {
        DB::rollBack();
    }
}
