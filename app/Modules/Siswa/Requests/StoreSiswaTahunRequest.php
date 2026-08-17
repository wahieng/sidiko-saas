<?php

namespace App\Modules\Siswa\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiswaTahunRequest extends FormRequest
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

            'tahun_ajaran_id' => [
                'required',
                'integer',
                'exists:tahun_ajaran,id',
            ],

            'kelompok_rombel_id' => [
                'required',
                'integer',
                'exists:kelompok_rombel,id',
            ],

            'status' => [
                'required',
                Rule::in([
                    'AKTIF',
                    'LULUS',
                    'PINDAH',
                    'KELUAR',
                ]),
            ],

            'tanggal_masuk' => [
                'nullable',
                'date',
            ],

            'tanggal_keluar' => [
                'nullable',
                'date',
                'after_or_equal:tanggal_masuk',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }
}