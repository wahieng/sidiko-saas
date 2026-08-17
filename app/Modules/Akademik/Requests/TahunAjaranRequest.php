<?php

namespace App\Modules\Akademik\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TahunAjaranRequest extends FormRequest
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
                'max:20',
                Rule::unique('tahun_ajaran', 'kode')
                    ->ignore($this->route('tahunAjaran')),
            ],

            'nama' => [
                'required',
                'string',
                'max:100',
            ],

            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after:tanggal_mulai',
            ],

            'aktif' => [
                'boolean',
            ],
        ];
    }
}