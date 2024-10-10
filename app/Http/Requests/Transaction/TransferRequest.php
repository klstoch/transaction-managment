<?php

declare(strict_types=1);

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $recipient_email
 * @property float $amount
 * @property string $currency
 */
class TransferRequest extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1|max:100000',
            'currency' => 'required|string|in:RUB,USD,EUR',
            'recipient_email' => 'required|email|exists:users,email',
        ];
    }
}
