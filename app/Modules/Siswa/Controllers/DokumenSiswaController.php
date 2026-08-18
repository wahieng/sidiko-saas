<?php

namespace App\Modules\Siswa\Controllers;

use App\Modules\Siswa\Models\Siswa;
use App\Modules\Siswa\Requests\StoreDokumenSiswaRequest;
use App\Modules\Siswa\Services\DokumenSiswaService;
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