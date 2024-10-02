<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransactionType;
use App\ValueObject\MoneyVO;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'recipient_id',
        'currency'
    ];

    protected $casts = [
        'type' => TransactionType::class,
    ];

    public static function createForDeposit(
        User    $user,
        MoneyVO $money,
    ): self
    {
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->type = TransactionType::DEPOSIT;
        $transaction->amount = $money->getAmount();
        $transaction->currency = $money->getCurrency();
        $transaction->recipient_id = $user->id;

//        $transaction->user()->associate($user);

        return $transaction;
    }

    public static function createForWithdraw(
        User   $user,
        MoneyVO $money,
    ): self
    {
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->type = TransactionType::WITHDRAW;
        $transaction->amount = $money->getAmount();
        $transaction->currency = $money->getCurrency();
        $transaction->recipient_id = null;

        //$transaction->user()->associate($user);

        return $transaction;
    }

    public static function createForTransfer(
        User    $sender,
        MoneyVO $money,
        User    $recipient,
    ): self
    {
        $transaction = new Transaction();
        $transaction->user_id = $sender->id;
        $transaction->type = TransactionType::TRANSFER;
        $transaction->amount = $money->getAmount();
        $transaction->currency = $money->getCurrency();
        $transaction->recipient_id = $recipient->id;

        //$transaction->user()->associate($sender);

        return $transaction;
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
