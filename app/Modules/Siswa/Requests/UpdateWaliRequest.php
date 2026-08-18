<?php

namespace App\Modules\Siswa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWaliRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'nik' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
            ],

            'hubungan' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'tempat_lahir' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'tanggal_lahir' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'pendidikan' => [
                'sometimes',
                'nullable',
                'string',
                'max:100',
            ],

            'pekerjaan' => [
                'sometimes',
                'nullable',
                'string',
                'max:150',
            ],

            'penghasilan' => [
                'sometimes',
                'nullable',
                'numeric',
                'min:0',
            ],

            'no_hp' => [
                'sometimes',
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
            ],

            'alamat' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'keterangan' => [
                'sometimes',
                'nullable',
                'string',
            ],
        ];
    }
}