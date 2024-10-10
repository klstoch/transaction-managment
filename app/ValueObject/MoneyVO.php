<?php

declare(strict_types=1);

namespace App\ValueObject;

use Chetkov\Money\Exception\MoneyException;
use Chetkov\Money\Money;

final readonly class MoneyVO
{
    private const SUPPORTED_CURRENCIES = ['USD', 'EUR', 'RUB'];

    private function __construct(
        private Money $money,
    ) {}

    public static function create(float $amount, string $currency): self
    {
        self::validateAmount($amount);

        self::validateCurrency($currency);

        return self::exceptionHandling(function () use ($amount, $currency) {
            $money = new Money($amount, $currency);

            return new self($money);
        });
    }

    private function createFromMoney(Money $money): self
    {
        return new self($money);
    }

    public function getAmount(): float
    {
        return $this->money->getAmount();
    }

    public function getCurrency(): string
    {
        return $this->money->getCurrency();
    }

    public function exchange(string $toCurrency): self
    {
        self::validateCurrency($toCurrency);

        return $this->exceptionHandling(function () use ($toCurrency) {
            if ($this->money->getCurrency() !== $toCurrency) {
                $money = $this->money->exchange($toCurrency);

                return self::createFromMoney($money);
            }

            return $this;
        });
    }

    public function add(self $other): self
    {
        return $this->exceptionHandling(function () use ($other) {
            $money = $this->money->add($other->money);

            return self::createFromMoney($money);
        });
    }

    public function subtract(self $other): self
    {

        return $this->exceptionHandling(function () use ($other) {
            $money = $this->money->subtract($other->money);

            return self::createFromMoney($money);
        });

    }

    private static function validateAmount(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('The amount must be at least 0.');
        }
    }

    private static function validateCurrency(string $currency): void
    {
        if (! in_array($currency, self::SUPPORTED_CURRENCIES, true)) {
            throw new \InvalidArgumentException("Currency '$currency' not supported.");
        }
    }

    private static function exceptionHandling(callable $callback): self
    {
        try {
            return $callback();
        } catch (MoneyException $e) {
            throw new \RuntimeException($e->getMessage(), previous: $e);
        }
    }
}
