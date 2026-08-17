<?php

namespace App\Core\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriptionRequest extends FormRequest
{
    /**
     * Izinkan request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi.
     */
    public function rules(): array
    {
        return [
            'paket_langganan_id' => [
                'required',
                'integer',
                'exists:paket_langganan,id',
            ],
        ];
    }

    /**
     * Pesan validasi.
     */
    public function messages(): array
    {
        return [
            'paket_langganan_id.required' =>
                'Paket langganan wajib dipilih.',

            'paket_langganan_id.exists' =>
                'Paket langganan tidak ditemukan.',
        ];
    }
}