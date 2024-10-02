<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\User\UserRepository;
use App\ValueObject\MoneyVO;
use Throwable;

readonly class WithdrawService
{

    public function __construct(
        private DbTransactionManagerService $dbTransactionManagerService,
        private UserRepository              $userRepository,
        private TransactionRepository       $transactionRepository,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function withdraw(User $user, MoneyVO $money): void
    {
        $this->dbTransactionManagerService->begin();

        try {
            $transaction = $user->withdraw($money);
            $this->userRepository->save($user);

            $this->transactionRepository->save($transaction);

            $this->dbTransactionManagerService->commit();

        } catch (Throwable $exception) {
            $this->dbTransactionManagerService->rollback();
            throw $exception;
        }
    }
}
