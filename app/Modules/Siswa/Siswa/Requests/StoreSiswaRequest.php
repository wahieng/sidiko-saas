<?php

namespace App\Modules\Siswa\Siswa\Requests;

use App\Core\Tenant\Context\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = app(TenantContext::class)->require()->id;

        return [
            'nis' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('siswa', 'nis')
                    ->where(fn ($query) => $query->where(
                        'tenant_id',
                        $tenantId
                    )),
            ],

            'nisn' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('siswa', 'nisn')
                    ->where(fn ($query) => $query->where(
                        'tenant_id',
                        $tenantId
                    )),
            ],

            'nik' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('siswa', 'nik')
                    ->where(fn ($query) => $query->where(
                        'tenant_id',
                        $tenantId
                    )),
            ],

            'no_kk' => [
                'nullable',
                'string',
                'max:20',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'nama_panggilan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'jenis_kelamin' => [
                'required',
                'in:L,P',
            ],

            'tempat_lahir' => [
                'nullable',
                'string',
                'max:100',
            ],

            'tanggal_lahir' => [
                'nullable',
                'date',
            ],

            'agama' => [
                'nullable',
                'string',
                'max:50',
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

            'rt' => [
                'nullable',
                'string',
                'max:5',
            ],

            'rw' => [
                'nullable',
                'string',
                'max:5',
            ],

            'desa' => [
                'nullable',
                'string',
                'max:100',
            ],

            'kecamatan' => [
                'nullable',
                'string',
                'max:100',
            ],

            'kabupaten' => [
                'nullable',
                'string',
                'max:100',
            ],

            'provinsi' => [
                'nullable',
                'string',
                'max:100',
            ],

            'kode_pos' => [
                'nullable',
                'string',
                'max:10',
            ],

            'anak_ke' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'jumlah_saudara' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'jenis_tinggal' => [
                'nullable',
                'string',
                'max:100',
            ],

            'transportasi' => [
                'nullable',
                'string',
                'max:100',
            ],

            'kebutuhan_khusus' => [
                'nullable',
                'string',
                'max:255',
            ],

            'foto' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}