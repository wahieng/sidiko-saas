<?php

namespace App\Modules\Siswa\DokumenSiswa\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokumenSiswaRequest extends FormRequest
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

            'jenis_dokumen' => [
                'required',
                'string',
                'max:100',
            ],

            'file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,jpg,jpeg,png',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.required' => 'Siswa wajib dipilih.',
            'siswa_id.exists' => 'Siswa tidak ditemukan.',

            'jenis_dokumen.required' => 'Jenis dokumen wajib diisi.',

            'file.required' => 'File dokumen wajib diunggah.',
            'file.file' => 'File dokumen tidak valid.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
            'file.mimes' => 'File harus berupa PDF, JPG, JPEG, atau PNG.',

            'keterangan.max' => 'Keterangan maksimal 1000 karakter.',
        ];
    }
}