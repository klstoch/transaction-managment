<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionType;
use App\ValueObject\MoneyVO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 * @property TransactionType $type
 * @property float $amount
 * @property string $currency
 * @property int|null $recipient_id
 */
class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'recipient_id',
        'currency',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'type' => TransactionType::class,
    ];

    public static function createForDeposit(
        User $user,
        MoneyVO $money,
    ): self {

        if (! $user->exists) {
            $user->save();
        }

        $transaction = new Transaction;
        $transaction->type = TransactionType::DEPOSIT;
        $transaction->amount = $money->getAmount();
        $transaction->currency = $money->getCurrency();
        $transaction->recipient_id = $user->id;

        $user->addTransaction($transaction);

        return $transaction;
    }

    public static function createForWithdraw(
        User $user,
        MoneyVO $money,
    ): self {
        $transaction = new Transaction;
        $transaction->user_id = $user->id;
        $transaction->type = TransactionType::WITHDRAW;
        $transaction->amount = $money->getAmount();
        $transaction->currency = $money->getCurrency();
        $transaction->recipient_id = null;

        $user->addTransaction($transaction);

        return $transaction;
    }

    public static function createForTransfer(
        User $sender,
        MoneyVO $money,
        User $recipient,
    ): self {
        $transaction = new Transaction;
        $transaction->type = TransactionType::TRANSFER;
        $transaction->amount = $money->getAmount();
        $transaction->currency = $money->getCurrency();
        $transaction->recipient_id = $recipient->id;

        $sender->addTransaction($transaction);

        return $transaction;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
