<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObject;

use App\ValueObject\MoneyVO;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class MoneyVOExceptionTest extends TestCase
{
    #[DataProvider('unsupportedCurrencyDataProvider')]
    public function testCreateWithUnsupportedCurrency(string $currency): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Currency '$currency' not supported.");

        MoneyVO::create(100, $currency);
    }

    public static function unsupportedCurrencyDataProvider(): array
    {
        return [
            ['GBP'],
            ['JPY'],
            ['CAD'],
        ];
    }

    #[DataProvider('negativeAmountDataProvider')]
    public function testCreateWithNegativeAmount(float $amount): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The amount must be at least 0.');

        MoneyVO::create($amount, 'RUB');
    }

    public static function negativeAmountDataProvider(): array
    {
        return [
            [-1],
            [-100],
            [-0.01],
        ];
    }

    public function testExchangeToUnsupportedCurrency(): void
    {
        $money = MoneyVO::create(100, 'RUB');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Currency 'GBP' not supported.");

        $money->exchange('GBP');
    }
}
