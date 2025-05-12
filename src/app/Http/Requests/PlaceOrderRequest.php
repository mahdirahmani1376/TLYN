<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => ['required', 'decimal:0,3'],
            'price' => ['required', 'integer']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
