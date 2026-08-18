<?php

namespace App\Modules\Siswa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWaliRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'nik' => [
                'nullable',
                'string',
                'max:20',
            ],

            'hubungan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tempat_lahir' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tanggal_lahir' => [
                'nullable',
                'date',
            ],

            'pendidikan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'pekerjaan' => [
                'nullable',
                'string',
                'max:150',
            ],

            'penghasilan' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'alamat' => [
                'nullable',
                'string',
            ],

            'keterangan' => [
                'nullable',
                'string',
            ],
        ];
    }
}