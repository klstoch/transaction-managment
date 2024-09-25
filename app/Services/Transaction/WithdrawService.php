<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Factory\TransactionFactory;
use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\User\UserRepository;
use Throwable;

readonly class WithdrawService
{

    public function __construct(
        private DbTransactionManagerService  $dbTransactionManagerService,
        private UserRepository             $userRepository,
        private TransactionRepository        $transactionRepository,
        //private TransactionFactory          $transactionFactory
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function withdraw(User $user, float $amount, string $currency = 'RUB'): void
    {
        $this->dbTransactionManagerService->begin();

        try {
            $transaction = $user->withdraw($amount, $currency);
            $this->userRepository->save($user);

            //$transaction = $this->transactionFactory->createForWithdraw($user, $amount, $currency);
            $this->transactionRepository->save($transaction);

            $this->dbTransactionManagerService->commit();

        } catch (Throwable $exception) {
            $this->dbTransactionManagerService->rollback();
            throw $exception;
        }
    }
}
