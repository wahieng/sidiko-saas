<?php

namespace App\Modules\Siswa\DokumenSiswa\Controllers;

use App\Modules\Siswa\Siswa\Models\Siswa;
use App\Modules\Siswa\DokumenSiswa\Requests\StoreDokumenSiswaRequest;
use App\Modules\Siswa\DokumenSiswa\Services\DokumenSiswaService;
use Illuminate\Http\RedirectResponse;

class DokumenSiswaController
{
    public function __construct(
        protected DokumenSiswaService $service
    ) {
    }

    public function store(
        StoreDokumenSiswaRequest $request
    ): RedirectResponse {
        $siswa = Siswa::query()
            ->findOrFail(
                $request->integer('siswa_id')
            );

        $this->service->store(
            $siswa,
            $request->file('file'),
            $request->string('jenis_dokumen')->toString(),
            $request->input('keterangan')
        );

        return back()->with(
            'success',
            'Dokumen siswa berhasil diunggah.'
        );
    }
}