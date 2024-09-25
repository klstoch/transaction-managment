<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\DTO\TransferData;
use App\Factory\TransactionFactory;
use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\User\UserRepository;
use Exception;
use Throwable;

readonly class TransferService
{

    public function __construct(
        private DbTransactionManagerService $dbTransactionManagerService,
        private UserRepository              $userRepository,
        private TransactionRepository        $transactionRepository,
        //private TransactionFactory          $transactionFactory,
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function transfer(User $sender, float $amount, string $recipientEmail, string $currency = 'RUB'): void
    {
        $this->dbTransactionManagerService->begin();

        try {
            $recipient = $this->userRepository->findByEmail($recipientEmail);
            if (!$recipient) {
                throw new Exception('Получатель не найден');
            }

            $transaction = $sender->transfer($recipient, $amount, $currency);

            $this->userRepository->save($sender);
            $this->userRepository->save($recipient);

            // $transaction = $this->transactionFactory->createForDeposit($recipient, $amount, $currency);
            $this->transactionRepository->save($transaction);

            $this->dbTransactionManagerService->commit();


        } catch (Throwable $exception) {
            $this->dbTransactionManagerService->rollback();
            throw $exception;
        }

    }
}
