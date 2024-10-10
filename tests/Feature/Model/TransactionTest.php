<?php

declare(strict_types=1);

namespace Tests\Feature\Model;

use App\Models\Transaction;
use App\Models\User;
use App\ValueObject\MoneyVO;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\LibConfig;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    /**
     * @throws RequiredParameterMissedException
     */
    public function testMultipleTransactions(): void
    {
        $config = require __DIR__.'/../../../config/money.php';
        LibConfig::getInstance($config);

        $user = User::create(
            'test',
            'test@test.com',
            'password',
            MoneyVO::create(100, 'RUB'),
        );
        $user->save();

        $recipient = User::create(
            'test_recipient',
            'test_recipient@test.com',
            'password_recipient',
            MoneyVO::create(0, 'RUB'),
        );
        $recipient->save();

        $expectedTransactions = [
            ['type' => 'deposit'],
            ['type' => 'withdraw'],
            ['type' => 'transfer'],
        ];

        $user->deposit(MoneyVO::create(50, 'RUB'));
        $user->withdraw(MoneyVO::create(30, 'RUB'));
        $user->transfer($recipient, MoneyVO::create(20, 'RUB'));

        $transactions = Transaction::query()->where('user_id', $user->id)->get();

        $this->assertCount(count($expectedTransactions), $transactions);

        $expectedTypes = ['deposit', 'withdraw', 'transfer'];
        foreach ($transactions as $index => $transaction) {
            $this->assertInstanceOf(Transaction::class, $transaction);
            $this->assertEquals($expectedTypes[$index], $transaction->type->value);
        }
    }
}
