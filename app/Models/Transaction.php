<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

/**
 * @property int $user_id
 * @property TransactionType $type
 * @property float $amount
 * @property string $currency
 * @property int $recipient_id
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'amount', 'recipient_id', 'currency'];

    protected $casts = [
        'type' => TransactionType::class,
        'amount' => 'float',
        'currency' => 'string',
    ];

    public static function createForDeposit(
        User   $user,
        float  $amount,
        string $currency,
    ): self
    {
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->type = TransactionType::DEPOSIT;
        $transaction->setAmount($amount);
        $transaction->currency = $currency;
        $transaction->recipient_id = $user->id;

        return $transaction;
    }

    /**
     * @throws Exception
     */
    public static function createForWithdraw(
        User   $user,
        float  $amount,
        string $currency,
    ): self
    {
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->type = TransactionType::WITHDRAW;
        $transaction->setAmount($amount);
        $transaction->currency = $currency;
        $transaction->recipient_id = null;

        return $transaction;
    }

    public static function createForTransfer(
        User   $sender,
        float  $amount,
        string $currency,
        User   $recipient,
    ): self
    {
        $transaction = new Transaction();
        $transaction->user_id = $sender->id;
        $transaction->type = TransactionType::TRANSFER;
        $transaction->setAmount($amount);
        $transaction->currency = $currency;
        $transaction->recipient_id = $recipient->id;

        return $transaction;
    }

    private function setAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Сумма транзакции должна быть больше 0');
        }
        $this->amount = $amount;
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
