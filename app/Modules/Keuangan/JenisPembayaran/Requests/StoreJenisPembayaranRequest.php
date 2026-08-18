<?php

namespace App\Modules\Keuangan\JenisPembayaran\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJenisPembayaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => [
                'required',
                'string',
                'max:50',
            ],

            'nama' => [
                'required',
                'string',
                'max:100',
            ],

            'kategori' => [
                'required',
                'in:BULANAN,SEMESTER,TAHUNAN,SEKALI',
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
            'kode.required' => 'Kode pembayaran wajib diisi.',
            'kode.max' => 'Kode pembayaran maksimal 50 karakter.',

            'nama.required' => 'Nama pembayaran wajib diisi.',
            'nama.max' => 'Nama pembayaran maksimal 100 karakter.',

            'kategori.required' => 'Kategori pembayaran wajib dipilih.',
            'kategori.in' => 'Kategori pembayaran tidak valid.',

            'keterangan.string' => 'Keterangan harus berupa teks.',

            'aktif.boolean' => 'Status aktif tidak valid.',
        ];
    }
}