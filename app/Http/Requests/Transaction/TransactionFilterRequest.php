<?php

declare(strict_types=1);

namespace App\Http\Requests\Transaction;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class TransactionFilterRequest extends FormRequest
{
    /**
     * @return array<string, array<string>>
     */
    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'in:deposit,withdraw,transfer'],
            'from_date' => ['sometimes', 'date_format:Y-m-d'],
            'to_date' => ['sometimes', 'date_format:Y-m-d'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        $user = $this->user();

        if ($user instanceof User) {
            $userId = $user->id;
        } else {
            $userId = null;
        }

        return array_filter([
            'type' => $this->input('type'),
            'from_date' => $this->input('from_date'),
            'to_date' => $this->input('to_date'),
            'user_id' => $userId,
        ]);
    }
}
