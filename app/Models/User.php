<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\MoneyVOCast;
use App\ValueObject\MoneyVO;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read string $password
 * @property-read MoneyVO $balance
 * @property mixed $transactions
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
        'balance',
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

    public static function create(
        string $name,
        string $email,
        string $password,
        ?MoneyVO $initialBalance = null,
    ): self {
        $user = new self();

        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_DEFAULT);
        $user->balance = $initialBalance ?? MoneyVO::create(0, 'RUB');

        return $user;
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
            'balance' => MoneyVOCast::class,
        ];
    }

    public function checkPassword(string $password): bool
    {
        if (!Hash::check($password, $this->password)) {
            throw new InvalidArgumentException('Неправильный пароль.');
        }
        return true;
    }

    public function deposit(MoneyVO $money): Transaction
    {
        $this->validateAmountInDefaultCurrency($money);

        $convertedMoney = $money->exchange($this->balance->getCurrency());
        $this->balance = $this->balance->add($convertedMoney);
        return Transaction::createForDeposit($this, $money);
    }

    public function withdraw(MoneyVO $money): Transaction
    {
        $this->validateAmountInDefaultCurrency($money);
        $this->validateBalance($money);

        $convertedMoney = $money->exchange($this->balance->getCurrency());

        $this->balance = $this->balance->subtract($convertedMoney);
        return Transaction::createForWithdraw($this, $money);
    }

    public function transfer(User $recipient, MoneyVO $money): Transaction
    {
        $this->validateAmountInDefaultCurrency($money);
        $this->validateBalance($money);

        if ($this->id === $recipient->id) {
            throw new InvalidArgumentException('Нельзя перевести средства самому себе');
        }

        $convertedSenderMoney = $money->exchange($this->balance->getCurrency());

        $this->balance = $this->balance->subtract($convertedSenderMoney);

        $convertedRecipientMoney = $money->exchange($recipient->balance->getCurrency());
        $recipient->balance = $recipient->balance->add($convertedRecipientMoney);

        return Transaction::createForTransfer($this, $money, $recipient);

    }

    private function validateAmountInDefaultCurrency(MoneyVO $money): void
    {

        $convertedMoney = $money->exchange($this->balance->getCurrency());
        $amount = $convertedMoney->getAmount();

        if ($amount < 1 || $amount > 100000) {
            throw new InvalidArgumentException('Сумма должна быть от 1 до 100000 в RUB');
        }
    }

    private function validateBalance(MoneyVO $money): void
    {
        $convertedMoney = $money->exchange($this->balance->getCurrency());

        if ($convertedMoney->getAmount() > $this->balance->getAmount()) {
            throw new InvalidArgumentException('Недостаточно средств');
        }
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function addTransaction(Transaction $transaction): self
    {
        $transaction->user()->associate($this);
        $this->transactions()->save($transaction);

        return $this;
    }
}
