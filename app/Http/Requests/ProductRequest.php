<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order' => 'in:lowest-to-highest,highest-to-lowest',
            'country_code' => 'nullable|string|max:3',
            'currency_code' => 'nullable|string|max:3',
            'date' => 'nullable|date',
        ];
    }
}
