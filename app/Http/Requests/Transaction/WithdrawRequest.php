<?php

declare(strict_types=1);


namespace App\Http\Requests\Transaction;

/**
 * @property float $amount
 * @property string $currency
 */
class WithdrawRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1|max:100000',
            'currency' => 'required|string|in:RUB,USD,EUR',
        ];
    }
}
