<?php

namespace App\Core\Billing\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jumlah' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'nomor_pembayaran' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tanggal_pembayaran' => [
                'nullable',
                'date',
            ],

            'metode' => [
                'required',
                'string',
                'max:50',
            ],

            'referensi' => [
                'nullable',
                'string',
                'max:255',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'jumlah.required' =>
                'Jumlah pembayaran wajib diisi.',

            'jumlah.numeric' =>
                'Jumlah pembayaran harus berupa angka.',

            'jumlah.gt' =>
                'Jumlah pembayaran harus lebih dari 0.',

            'tanggal_pembayaran.date' =>
                'Tanggal pembayaran tidak valid.',

            'metode.required' =>
                'Metode pembayaran wajib dipilih.',
        ];
    }
}