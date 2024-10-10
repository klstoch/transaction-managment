<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObject;

use App\ValueObject\MoneyVO;
use Chetkov\Money\Exception\RequiredParameterMissedException;
use Chetkov\Money\LibConfig;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyVOTest extends TestCase
{
    #[DataProvider('exchangeDataProvider')]
    public function testExchange(float $initialAmount, string $initialCurrency, string $toCurrency, float $expectedAmount): void
    {
        $money = MoneyVO::create($initialAmount, $initialCurrency);

        $exchangedMoney = $money->exchange($toCurrency);

        $this->assertEquals($expectedAmount, $exchangedMoney->getAmount());
        $this->assertEquals($toCurrency, $exchangedMoney->getCurrency());
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public static function exchangeDataProvider(): array
    {
        $config = require __DIR__.'/../../../config/money.php';
        LibConfig::getInstance($config);

        return [
            'rub_to_usd' => [100, 'RUB', 'USD', 1.1],
            'usd_to_eur' => [50, 'USD', 'EUR', 45.5],
            'eur_to_rub' => [10, 'EUR', 'RUB', 1040],
            'usd_to_rub' => [20, 'USD', 'RUB', 1900],
        ];
    }

    #[DataProvider('addDataProvider')]
    public function testAdd(float $amount1, string $currency1, float $amount2, string $currency2, float $expectedAmount): void
    {
        $money1 = MoneyVO::create($amount1, $currency1);
        $money2 = MoneyVO::create($amount2, $currency2);

        $result = $money1->add($money2);

        $this->assertEquals($expectedAmount, $result->getAmount());
        $this->assertEquals($currency1, $result->getCurrency());
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public static function addDataProvider(): array
    {
        $config = require __DIR__.'/../../../config/money.php';
        LibConfig::getInstance($config);

        return [
            'add_same_currency' => [100, 'RUB', 50, 'RUB', 150],
            'add_usd' => [20, 'USD', 30, 'USD', 50],
            'add_eur' => [10, 'EUR', 5, 'EUR', 15],
            'add_usd_to_rub' => [100, 'RUB', 1, 'USD', 195],
            'add_usd_to_eur' => [100, 'EUR', 100, 'USD', 191],
            'add_eur_to_rub' => [100, 'RUB', 10, 'EUR', 1140],
            'add_rub_to_usd' => [10, 'USD', 100, 'RUB', 11.1],
            'add_rub_to_eur' => [10, 'EUR', 100, 'RUB', 11],
        ];
    }

    #[DataProvider('subtractDataProvider')]
    public function testSubtract(float $amount1, string $currency1, float $amount2, string $currency2, float $expectedAmount): void
    {
        $money1 = MoneyVO::create($amount1, $currency1);
        $money2 = MoneyVO::create($amount2, $currency2);

        $result = $money1->subtract($money2);

        $this->assertEquals($expectedAmount, $result->getAmount());
        $this->assertEquals($currency1, $result->getCurrency());
    }

    /**
     * @throws RequiredParameterMissedException
     */
    public static function subtractDataProvider(): array
    {
        $config = require __DIR__.'/../../../config/money.php';
        LibConfig::getInstance($config);

        return [
            'subtract_same_currency' => [100, 'RUB', 50, 'RUB', 50],
            'subtract_usd' => [80, 'USD', 30, 'USD', 50],
            'subtract_eur' => [10, 'EUR', 5, 'EUR', 5],
            'subtract_usd_from_rub' => [100, 'RUB', 1, 'USD', 5],
            'subtract_usd_from_eur' => [100, 'EUR', 100, 'USD', 9],
            'subtract_eur_from_rub' => [1100, 'RUB', 10, 'EUR', 60],
            'subtract_rub_from_usd' => [10, 'USD', 100, 'RUB', 8.9],
            'subtract_rub_from_eur' => [10, 'EUR', 100, 'RUB', 9],
        ];

    }
}
