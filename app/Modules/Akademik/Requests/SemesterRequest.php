<?php

namespace App\Modules\Akademik\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SemesterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tahun_ajaran_id' => [
                'required',
                'integer',
                'exists:tahun_ajaran,id',
            ],

            'kode' => [
                'required',
                Rule::in([
                    'ganjil',
                    'genap',
                ]),
            ],

            'nama' => [
                'required',
                'string',
                'max:50',
            ],

            'tanggal_mulai' => [
                'required',
                'date',
            ],

            'tanggal_selesai' => [
                'required',
                'date',
                'after_or_equal:tanggal_mulai',
            ],

            'aktif' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran_id.required'
                => 'Tahun ajaran wajib dipilih.',

            'tahun_ajaran_id.exists'
                => 'Tahun ajaran tidak ditemukan.',

            'kode.required'
                => 'Kode semester wajib dipilih.',

            'kode.in'
                => 'Kode semester harus ganjil atau genap.',

            'nama.required'
                => 'Nama semester wajib diisi.',

            'tanggal_mulai.required'
                => 'Tanggal mulai wajib diisi.',

            'tanggal_selesai.required'
                => 'Tanggal selesai wajib diisi.',

            'tanggal_selesai.after_or_equal'
                => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai.',
        ];
    }
}