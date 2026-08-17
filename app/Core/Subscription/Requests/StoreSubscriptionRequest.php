<?php

namespace App\Core\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionRequest extends FormRequest
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
            'tenant_id' => [
                'required',
                'integer',
                'exists:tenants,id',
            ],

            'paket_langganan_id' => [
                'required',
                'integer',
                'exists:paket_langganan,id',
            ],

            'hari_trial' => [
                'nullable',
                'integer',
                'min:1',
                'max:365',
            ],
        ];
    }

    /**
     * Pesan validasi.
     */
    public function messages(): array
    {
        return [
            'tenant_id.required' =>
                'Tenant wajib dipilih.',

            'tenant_id.exists' =>
                'Tenant tidak ditemukan.',

            'paket_langganan_id.required' =>
                'Paket langganan wajib dipilih.',

            'paket_langganan_id.exists' =>
                'Paket langganan tidak ditemukan.',

            'hari_trial.integer' =>
                'Hari trial harus berupa angka.',

            'hari_trial.min' =>
                'Hari trial minimal 1 hari.',

            'hari_trial.max' =>
                'Hari trial maksimal 365 hari.',
        ];
    }
}