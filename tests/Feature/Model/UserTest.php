<?php

declare(strict_types=1);


namespace Tests\Feature\Model;

use App\Models\Transaction;
use App\Models\User;
use App\ValueObject\MoneyVO;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\LibConfig;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserTest extends TestCase
{
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
        $user->name = 'test';
        $user->email = 'test@test.com';
        $user->password = 'test';
        $user->balance = MoneyVO::create($initialBalance, 'RUB');
        $user->save();

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
            'zero_amount' => [100, MoneyVO::create(0, 'RUB'), 'RUB', 100, 0, 'deposit'],    // попытка пополнить 0
            'over_deposit' => [100, MoneyVO::create(101000, 'RUB'), 'RUB', 100, 0, 'deposit'], // попытка пополнить больше 100000
        ];
    }

    #[DataProvider('withdrawDataProvider')]
    public function testWithdraw(
        float   $initialBalance,
        MoneyVO $money,
        string  $expectedCurrency,
        float   $expectedBalance,
        float   $expectedTransactionAmount,
        string  $expectedTransactionType,
    ): void
    {

        $user = new User();
        $user->name = 'test';
        $user->email = 'test@test.com';
        $user->password = 'test';
        $user->balance = MoneyVO::create($initialBalance, 'RUB');
        $user->save();

        if ($money->getAmount() < 1 || $money->getAmount() > 100000 || $user->balance->getAmount() < $money->getAmount()) {
            $this->expectException(InvalidArgumentException::class);
            $user->withdraw($money);
            return;
        }

        $transaction = $user->withdraw($money);

        $this->assertEquals($expectedBalance, $user->balance->getAmount());
        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($expectedTransactionAmount, $transaction->amount);
        $this->assertEquals($expectedCurrency, $transaction->currency);
        $this->assertEquals($expectedTransactionType, $transaction->type->value);
    }

    /**
     * @return array[]
     * @throws RequiredParameterMissedException
     */
    public static function withdrawDataProvider(): array
    {
        $config = require __DIR__ . '/../../../config/money.php';
        LibConfig::getInstance($config);

        return [
            'successful_withdraw' => [100, MoneyVO::create(50, 'RUB'), 'RUB', 50, 50, 'withdraw'],
            'insufficient_funds' => [100, MoneyVO::create(150, 'RUB'), 'RUB', 100, 0, 'withdraw'],  // Баланс меньше суммы снятия
            'zero_withdrawal' => [100, MoneyVO::create(0, 'RUB'), 'RUB', 100, 0, 'withdraw'],     // Попытка снять 0
        ];
    }

    #[DataProvider('transferDataProvider')]
    public function testTransfer(
        float   $initialBalanceSender,
        float   $initialBalanceRecipient,
        MoneyVO $transferAmount,
        float   $expectedSenderBalance,
        float   $expectedRecipientBalance,
        string  $expectedTransactionCurrency,
        float   $expectedTransactionAmount,
        bool    $shouldThrowException = false
    ): void
    {
        $sender = new User();
        $sender->name = 'test';
        $sender->email = 'test@test.com';
        $sender->password = 'test';
        $sender->balance = MoneyVO::create($initialBalanceSender, 'RUB');
        $sender->save();

        $recipient = new User();
        $recipient->name = 'test_recipient';
        $recipient->email = 'test@test_recipient.com';
        $recipient->password = 'test_recipient';
        $recipient->balance = MoneyVO::create($initialBalanceRecipient, 'RUB');
        $recipient->save();

        if ($shouldThrowException) {
            $this->expectException(InvalidArgumentException::class);
            $sender->transfer($sender, $transferAmount);
            return;
        }

        $transaction = $sender->transfer($recipient, $transferAmount);

        $this->assertEquals($expectedSenderBalance, $sender->balance->getAmount());
        $this->assertEquals($expectedRecipientBalance, $recipient->balance->getAmount());

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($expectedTransactionAmount, $transaction->amount);
        $this->assertEquals($expectedTransactionCurrency, $transaction->currency);
        $this->assertEquals('transfer', $transaction->type->value);
    }

    /**
     * @return array[]
     * @throws RequiredParameterMissedException
     */
    public static function transferDataProvider(): array
    {
        $config = require __DIR__ . '/../../../config/money.php';
        LibConfig::getInstance($config);

        return [
            'successful_transfer' => [100, 50, MoneyVO::create(50, 'RUB'), 50, 100, 'RUB', 50, false],
            'insufficient_funds' => [100, 50, MoneyVO::create(150, 'RUB'), 100, 50, 'RUB', 0, true],  // Недостаточно средств
            'zero_transfer' => [100, 50, MoneyVO::create(0, 'RUB'), 100, 50, 'RUB', 0, true],        // Попытка перевода 0
            'self_transfer' => [100, 100, MoneyVO::create(50, 'RUB'), 100, 100, 'RUB', 0, true],     // Попытка перевода самому себе
        ];
    }

}
