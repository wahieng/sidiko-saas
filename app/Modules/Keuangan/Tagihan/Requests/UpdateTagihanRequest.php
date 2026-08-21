<?php

namespace App\Modules\Keuangan\Tagihan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagihanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nomor_tagihan' => [
                'sometimes',
                'string',
                'max:100',
            ],

            'nominal_awal' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'tipe_diskon' => [
                'nullable',
                'in:PERSEN,NOMINAL',
            ],

            'nilai_diskon' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'nominal_diskon' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'nominal' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'jumlah_dibayar' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'sisa_tagihan' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'tanggal_tagihan' => [
                'sometimes',
                'date',
            ],

            'tanggal_jatuh_tempo' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_tagihan',
            ],

            'status' => [
                'sometimes',
                'in:BELUM_BAYAR,SEBAGIAN,LUNAS,BATAL',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ];
    }
}