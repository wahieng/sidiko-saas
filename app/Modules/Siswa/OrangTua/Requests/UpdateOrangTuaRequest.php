<?php

namespace App\Modules\Siswa\OrangTua\Requests;

use App\Modules\Siswa\OrangTua\Models\OrangTua;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrangTuaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var OrangTua $orangTua */
        $orangTua = $this->route('orangTua');

        return [
            'siswa_id' => [
                'required',
                'integer',
                'exists:siswa,id',
            ],

            'hubungan' => [
                'required',
                Rule::in([
                    'AYAH',
                    'IBU',
                ]),
                Rule::unique('orang_tua')
                    ->where(
                        fn ($query) => $query->where(
                            'siswa_id',
                            $this->siswa_id
                        )
                    )
                    ->ignore(
                        $orangTua?->id
                    ),
            ],

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

            'no_kk' => [
                'nullable',
                'string',
                'max:20',
            ],

            'tempat_lahir' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tanggal_lahir' => [
                'nullable',
                'date',
            ],

            'pendidikan' => [
                'nullable',
                'string',
                'max:255',
            ],

            'pekerjaan' => [
                'nullable',
                'string',
                'max:255',
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