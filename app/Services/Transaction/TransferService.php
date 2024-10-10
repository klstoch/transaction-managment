<?php

declare(strict_types=1);

namespace App\Services\Transaction;

use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use App\Repositories\User\UserRepository;
use App\ValueObject\MoneyVO;
use Exception;
use Throwable;

readonly class TransferService
{
    public function __construct(
        private DbTransactionManagerService $dbTransactionManagerService,
        private UserRepository $userRepository,
        private TransactionRepository $transactionRepository,
    ) {}

    /**
     * @throws Throwable
     */
    public function transfer(User $sender, MoneyVO $money, string $recipientEmail): void
    {
        $this->dbTransactionManagerService->begin();

        try {
            $recipient = $this->userRepository->findByEmail($recipientEmail);
            if (! $recipient) {
                throw new Exception('Получатель не найден');
            }

            $transaction = $sender->transfer($recipient, $money);

            $this->userRepository->save($sender);
            $this->userRepository->save($recipient);

            $this->transactionRepository->save($transaction);

            $this->dbTransactionManagerService->commit();

        } catch (Throwable $exception) {
            $this->dbTransactionManagerService->rollback();
            throw $exception;
        }

    }
}
