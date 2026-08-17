<?php

namespace App\Core\Billing\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateBillingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'langganan_id' => [
                'required',
                'integer',
                'exists:langganan,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'langganan_id.required' =>
                'Langganan wajib dipilih.',

            'langganan_id.exists' =>
                'Data langganan tidak ditemukan.',
        ];
    }
}