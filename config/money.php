<?php

declare(strict_types=1);

use Chetkov\Money\Exchanger\ExchangerInterface;
use Chetkov\Money\Exchanger\RatesProvider\SimpleExchangeRatesProvider;
use Chetkov\Money\Exchanger\SimpleExchanger;

$exchangeRates = [
    'USD-RUB' => [66.34, 68.12], // Курсы покупки/продажи отличаются
    'EUR-RUB' => [72.42],        // Единый курс
    'JPY-RUB' => [0.61],         // ...
];

return [
    'is_currency_conversation_enabled' => true,
    'exchanger_factory' => static function () use ($exchangeRates): ExchangerInterface {
        //Фабрика класса обменника
        static $instance;
        if (null === $instance) {
            $ratesProvider = SimpleExchangeRatesProvider::getInstance($exchangeRates);
            $instance = new SimpleExchanger($ratesProvider);
        }
        return $instance;
    },
];
