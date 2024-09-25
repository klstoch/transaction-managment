<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Factory\TransactionFactory;
use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\User\UserRepository;
use Throwable;

readonly class DepositService
{
    public function __construct(
        private UserRepository              $userRepository,
        private DbTransactionManagerService $dbTransactionManagerService,
       // private TransactionFactory          $transactionFactory,
        private TransactionRepository       $transactionRepository,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function deposit(User $user, float $amount, string $currency = 'RUB'): void
    {
        $this->dbTransactionManagerService->begin();

        try {
            $transaction = $user->deposit($amount, $currency);
            $this->userRepository->save($user);
            $this->transactionRepository->save($transaction);

            //$transaction = $this->transactionFactory->createForDeposit($user, $amount, $currency);

            $this->dbTransactionManagerService->commit();

        } catch (Throwable $exception) {
            $this->dbTransactionManagerService->rollback();
            throw $exception;
        }
    }

}
