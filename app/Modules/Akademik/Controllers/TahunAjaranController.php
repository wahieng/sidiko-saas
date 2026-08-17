<?php

namespace App\Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\TahunAjaran;
use App\Modules\Akademik\Requests\TahunAjaranRequest;
use App\Modules\Akademik\Services\TahunAjaranService;
use Illuminate\Http\JsonResponse;

class TahunAjaranController
{
    public function __construct(
        protected TahunAjaranService $service
    ) {
    }

    /**
     * Daftar tahun ajaran tenant aktif.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->all()
        );
    }

    /**
     * Simpan tahun ajaran.
     */
    public function store(
        TahunAjaranRequest $request
    ): JsonResponse {
        $tahunAjaran = $this->service->create(
            $request->validated()
        );

        return response()->json(
            $tahunAjaran,
            201
        );
    }

    /**
     * Detail tahun ajaran.
     */
    public function show(
        TahunAjaran $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $tahunAjaran->load([
                'semesters',
            ])
        );
    }

    /**
     * Update tahun ajaran.
     */
    public function update(
        TahunAjaranRequest $request,
        TahunAjaran $tahunAjaran
    ): JsonResponse {
        $tahunAjaran = $this->service->update(
            $tahunAjaran,
            $request->validated()
        );

        return response()->json(
            $tahunAjaran
        );
    }

    /**
     * Nonaktifkan tahun ajaran.
     */
    public function destroy(
        TahunAjaran $tahunAjaran
    ): JsonResponse {
        $this->service->nonaktifkan(
            $tahunAjaran
        );

        return response()->json([
            'message' => 'Tahun ajaran berhasil dinonaktifkan.',
        ]);
    }

    /**
     * Aktifkan tahun ajaran.
     */
    public function aktifkan(
        TahunAjaran $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $this->service->aktifkan(
                $tahunAjaran
            )
        );
    }

    /**
     * Ambil semester tahun ajaran.
     */
    public function semester(
        TahunAjaran $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $tahunAjaran
                ->semesters()
                ->orderBy('tanggal_mulai')
                ->get()
        );
    }
}