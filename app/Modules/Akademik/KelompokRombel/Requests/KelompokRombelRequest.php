<?php

namespace App\Modules\Akademik\KelompokRombel\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KelompokRombelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('kelompokRombel')?->id;

        return [
            'tahun_ajaran_id' => [
                'required',
                'integer',
                'exists:tahun_ajaran,id',
            ],

            'rombel_id' => [
                'required',
                'integer',
                'exists:rombel,id',
            ],

            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('kelompok_rombel', 'kode')
                    ->where(
                        fn ($query) => $query
                            ->where(
                                'tahun_ajaran_id',
                                $this->tahun_ajaran_id
                            )
                            ->where(
                                'rombel_id',
                                $this->rombel_id
                            )
                    )
                    ->ignore($id),
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