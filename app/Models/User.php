<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Transaction\TransferService;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property float $balance
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'currency',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $attributes = [
        'balance' => 0,
        'currency' => 'RUB',
    ];

    public function deposit(float $amount, string $currency): Transaction
    {
        $this->validateAmount($amount);

        $transaction = Transaction::createForDeposit($this, $amount, $currency);
        $this->balance += $amount;

        return $transaction;
    }

    /**
     * @throws Exception
     */
    public function withdraw(float $amount, string $currency): Transaction
    {
        $this->validateAmount($amount);
        $this->validateBalance($amount);

        $transaction = Transaction::createForWithdraw($this, $amount, $currency);
        $this->balance -= $amount;

        return $transaction;
    }

    /**
     * @throws Exception
     */
    public function transfer(User $recipient, float $amount, string $currency): Transaction
    {
        $this->validateAmount($amount);
        $this->validateBalance($amount);

        if ($this->id === $recipient->id) {
            throw new Exception('Нельзя перевести средства самому себе');
        }

        $transaction = Transaction::createForTransfer($this, $amount, $currency, $recipient);
        $this->balance -= $amount;
        $recipient->balance += $amount;

        return $transaction;
    }

    private function validateAmount(float $amount): void
    {
        if ($amount < 1 || $amount > 100000) {
            throw new InvalidArgumentException('Сумма транзакции должна быть от 1 до 100000');
        }
    }

    /**
     * @throws Exception
     */
    private function validateBalance(float $amount): void
    {
        if ($amount > $this->balance) {
            throw new Exception('Недостаточно средств');
        }
    }

    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'float',
        ];
    }
}
