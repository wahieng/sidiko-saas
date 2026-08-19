<?php

namespace App\Modules\Keuangan\TarifPembayaran\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTarifPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_pembayaran_id' => [
                'required',
                'integer',
                'exists:jenis_pembayaran,id',
            ],

            'kelompok_rombel_id' => [
                'required',
                'integer',
                'exists:kelompok_rombel,id',
            ],

            'nominal' => [
                'required',
                'numeric',
                'min:0',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],

            'aktif' => [
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'jenis_pembayaran_id.required' =>
                'Jenis pembayaran wajib dipilih.',

            'jenis_pembayaran_id.integer' =>
                'Jenis pembayaran tidak valid.',

            'jenis_pembayaran_id.exists' =>
                'Jenis pembayaran tidak ditemukan.',

            'kelompok_rombel_id.required' =>
                'Kelompok rombel wajib dipilih.',

            'kelompok_rombel_id.integer' =>
                'Kelompok rombel tidak valid.',

            'kelompok_rombel_id.exists' =>
                'Kelompok rombel tidak ditemukan.',

            'nominal.required' =>
                'Nominal tarif wajib diisi.',

            'nominal.numeric' =>
                'Nominal tarif harus berupa angka.',

            'nominal.min' =>
                'Nominal tarif tidak boleh kurang dari 0.',

            'keterangan.string' =>
                'Keterangan harus berupa teks.',

            'aktif.boolean' =>
                'Status aktif tidak valid.',
        ];
    }
}