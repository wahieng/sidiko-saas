<?php

namespace App\Modules\Siswa\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiswaTahunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kelompok_rombel_id' => [
                'sometimes',
                'integer',
                'exists:kelompok_rombel,id',
            ],

            'status' => [
                'sometimes',
                Rule::in([
                    'AKTIF',
                    'LULUS',
                    'PINDAH',
                    'KELUAR',
                ]),
            ],

            'tanggal_masuk' => [
                'sometimes',
                'nullable',
                'date',
            ],

            'tanggal_keluar' => [
                'sometimes',
                'nullable',
                'date',
                'after_or_equal:tanggal_masuk',
            ],

            'keterangan' => [
                'sometimes',
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}