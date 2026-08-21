<?php

namespace App\Modules\Keuangan\Tagihan\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagihanRequest extends FormRequest
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

            'nomor_tagihan' => [
                'required',
                'string',
                'max:100',
            ],

            'nominal_awal' => [
                'required',
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
                'required',
                'numeric',
                'min:0',
            ],

            'jumlah_dibayar' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'sisa_tagihan' => [
                'required',
                'numeric',
                'min:0',
            ],

            'tanggal_tagihan' => [
                'required',
                'date',
            ],

            'tanggal_jatuh_tempo' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_tagihan',
            ],

            'status' => [
                'required',
                'in:BELUM_BAYAR,SEBAGIAN,LUNAS,BATAL',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ];
    }
}