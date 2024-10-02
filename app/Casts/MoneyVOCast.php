<?php

namespace App\Casts;

use App\ValueObject\MoneyVO;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class MoneyVOCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): MoneyVO
    {
        $amount = $attributes['amount'] ?? 0;
        $currency = $attributes['currency'] ?? 'RUB';  // По умолчанию рубли

        return MoneyVO::create((float)$amount, $currency);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {

        if (!$value instanceof MoneyVO) {
            throw new \InvalidArgumentException('Значение должно быть экземпляром MoneyVO');
        }

        return [
            'amount' => $value->getAmount(),
            'currency' => $value->getCurrency(),
        ];
    }
}
