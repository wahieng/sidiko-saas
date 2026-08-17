<?php

namespace App\Modules\Siswa\Requests;

use App\Core\Tenant\Context\TenantContext;
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
        $tenantId = app(TenantContext::class)->require()->id;

        return [
            'siswa_id' => [
                'required',
                'integer',

                Rule::exists('siswa', 'id')
                    ->where(fn ($query) => $query->where(
                        'tenant_id',
                        $tenantId
                    )),
            ],

            'tahun_ajaran_id' => [
                'required',
                'integer',

                Rule::exists('tahun_ajaran', 'id')
                    ->where(fn ($query) => $query->where(
                        'tenant_id',
                        $tenantId
                    )),
            ],

            'kelompok_rombel_id' => [
                'required',
                'integer',

                Rule::exists('kelompok_rombel', 'id')
                    ->where(fn ($query) => $query
                        ->where('tenant_id', $tenantId)
                        ->where(
                            'tahun_ajaran_id',
                            $this->input('tahun_ajaran_id')
                        )
                    ),
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