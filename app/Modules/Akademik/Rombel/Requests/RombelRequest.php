<?php

namespace App\Modules\Akademik\Rombel\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RombelRequest extends FormRequest
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
                Rule::unique('rombel', 'kode')
                    ->ignore($this->route('rombel')),
            ],

            'nama' => [
                'required',
                'string',
                'max:100',
            ],

            'urutan' => [
                'required',
                'integer',
                'min:1',
            ],

            'aktif' => [
                'boolean',
            ],
        ];
    }
}