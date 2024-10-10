<?php

declare(strict_types=1);

use Chetkov\Money\Exchanger\ExchangerInterface;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use Chetkov\Money\Exchanger\SimpleExchanger;

$exchangeRates = [
    'USD-RUB' => [95],
    'RUB-USD' => [0.011],

    'EUR-RUB' => [104],
    'RUB-EUR' => [0.01],

    'USD-EUR' => [0.91],
    'EUR-USD' => [1.09],
];

return [
    'is_currency_conversation_enabled' => true,
    'exchanger_factory' => static function () use ($exchangeRates): ExchangerInterface {
        static $instance;
        if ($instance === null) {
            $ratesProvider = SimpleExchangeRatesProvider::getInstance($exchangeRates);
            $instance = new SimpleExchanger($ratesProvider);
        }

        return $instance;
    },
];
