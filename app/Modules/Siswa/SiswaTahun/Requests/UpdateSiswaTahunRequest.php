<?php

namespace App\Modules\Siswa\SiswaTahun\Requests;

use App\Core\Tenant\Context\TenantContext;
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
        $tenantId = app(TenantContext::class)->require()->id;

        $siswaTahun = $this->route('siswaTahun');

        return [
            'kelompok_rombel_id' => [
                'sometimes',
                'integer',

                Rule::exists('kelompok_rombel', 'id')
                    ->where(fn ($query) => $query
                        ->where('tenant_id', $tenantId)
                        ->where(
                            'tahun_ajaran_id',
                            $siswaTahun?->tahun_ajaran_id
                        )
                    ),
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