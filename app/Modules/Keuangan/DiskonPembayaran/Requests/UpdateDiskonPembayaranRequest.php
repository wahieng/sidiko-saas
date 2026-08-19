<?php

namespace App\Modules\Keuangan\DiskonPembayaran\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDiskonPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_id' => [
                'required',
                'integer',
                'exists:siswa,id',
            ],

            'tarif_pembayaran_id' => [
                'required',
                'integer',
                'exists:tarif_pembayaran,id',
            ],

            'tipe_diskon' => [
                'required',
                Rule::in([
                    'PERSEN',
                    'NOMINAL',
                ]),
            ],

            'nilai' => [
                'required',
                'numeric',
                'min:0',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tanggal_mulai' => [
                'nullable',
                'date',
            ],

            'tanggal_selesai' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'aktif' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (
                $this->tipe_diskon === 'PERSEN' &&
                $this->nilai > 100
            ) {
                $validator->errors()->add(
                    'nilai',
                    'Diskon persen tidak boleh lebih dari 100%.'
                );
            }
        });
    }
}