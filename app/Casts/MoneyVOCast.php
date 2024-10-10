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
        $amount = $attributes['balance'] ?? 0;
        $currency = $attributes['currency'] ?? 'RUB';

        return MoneyVO::create((float) $amount, (string) $currency);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {

        if (! $value instanceof MoneyVO) {
            throw new \InvalidArgumentException('Значение должно быть экземпляром MoneyVO');
        }

        return [
            'balance' => $value->getAmount(),
            'currency' => $value->getCurrency(),
        ];
    }
}
