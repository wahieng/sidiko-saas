<?php

namespace App\Modules\Keuangan\Tagihan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateTagihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'siswa_tahun_id' => [
                'required',
                'integer',
                'exists:siswa_tahun,id',
            ],

            'tarif_pembayaran_id' => [
                'required',
                'integer',
                'exists:tarif_pembayaran,id',
            ],

            'tanggal_tagihan' => [
                'required',
                'date',
            ],

            'tanggal_jatuh_tempo' => [
                'required',
                'date',
                'after_or_equal:tanggal_tagihan',
            ],
        ];
    }
}