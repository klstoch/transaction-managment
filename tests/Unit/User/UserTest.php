<?php

declare(strict_types=1);


namespace Tests\Unit\User;

use App\Models\Transaction;
use App\Models\User;
use App\ValueObject\MoneyVO;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\LibConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('depositDataProvider')]
    public function testDeposit(
        float   $initialBalance,
        MoneyVO $money,
        string  $expectedCurrency,
        float   $expectedBalance,
        float   $expectedTransactionAmount,
        string  $expectedTransactionType,
    ): void
    {
        $user = new User();
        $user->balance = MoneyVO::create($initialBalance, 'RUB');

        if ($money->getAmount() < 1 || $money->getAmount() > 100000) {
            $this->expectException(InvalidArgumentException::class);
            $user->deposit($money);
            return;
        }

        $transaction = $user->deposit($money);

        $this->assertEquals($expectedBalance, $user->balance->getAmount());
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($expectedTransactionAmount, $transaction->amount);
        $this->assertEquals($expectedCurrency, $transaction->currency);
        $this->assertEquals($expectedTransactionType, $transaction->type->value);

    }

    /**
     * @return array[]
     *
     * @throws RequiredParameterMissedException
     */
    public static function depositDataProvider(): array
    {

        $config = require __DIR__ . '/../../../config/money.php';
        LibConfig::getInstance($config);

        return [
            'successful_deposit' => [100, MoneyVO::create(50, 'RUB'), 'RUB', 150, 50, 'deposit'],
            'zero_amount' => [100, MoneyVO::create(0, 'RUB'), 'RUB', 100, 0, 'deposit'],
            'over_deposit' => [100, MoneyVO::create(101000, 'RUB'), 'RUB', 100, 0, 'deposit'],
        ];
    }

}
