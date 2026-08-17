<?php

namespace App\Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\KelompokRombel;
use App\Modules\Akademik\Requests\KelompokRombelRequest;
use App\Modules\Akademik\Services\KelompokRombelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KelompokRombelController
{
    public function __construct(
        protected KelompokRombelService $service
    ) {
    }

    /**
     * Daftar kelompok rombel berdasarkan tahun ajaran.
     */
    public function index(Request $request): JsonResponse
    {
        $tahunAjaranId = $request->integer(
            'tahun_ajaran_id'
        );

        if ($tahunAjaranId <= 0) {
            return response()->json([
                'message' => 'tahun_ajaran_id wajib diisi.',
            ], 422);
        }

        return response()->json(
            $this->service->byTahunAjaran(
                $tahunAjaranId
            )
        );
    }

    /**
     * Simpan kelompok rombel.
     */
    public function store(
        KelompokRombelRequest $request
    ): JsonResponse {
        $kelompokRombel = $this->service->create(
            $request->validated()
        );

        return response()->json(
            $kelompokRombel->load([
                'tahunAjaran',
                'rombel',
            ]),
            201
        );
    }

    /**
     * Detail kelompok rombel.
     */
    public function show(
        KelompokRombel $kelompokRombel
    ): JsonResponse {
        return response()->json(
            $kelompokRombel->load([
                'tahunAjaran',
                'rombel',
            ])
        );
    }

    /**
     * Update kelompok rombel.
     */
    public function update(
        KelompokRombelRequest $request,
        KelompokRombel $kelompokRombel
    ): JsonResponse {
        $kelompokRombel = $this->service->update(
            $kelompokRombel,
            $request->validated()
        );

        return response()->json(
            $kelompokRombel->load([
                'tahunAjaran',
                'rombel',
            ])
        );
    }

    /**
     * Nonaktifkan kelompok rombel.
     */
    public function destroy(
        KelompokRombel $kelompokRombel
    ): JsonResponse {
        $this->service->nonaktifkan(
            $kelompokRombel
        );

        return response()->json([
            'message' => 'Kelompok rombel berhasil dinonaktifkan.',
        ]);
    }

    /**
     * Ambil kelompok rombel aktif berdasarkan tahun ajaran.
     */
    public function byTahunAjaran(
        int $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $this->service->aktifByTahunAjaran(
                $tahunAjaran
            )
        );
    }

    /**
     * Ambil kelompok rombel berdasarkan Rombel
     * pada tahun ajaran tertentu.
     */
    public function byRombel(
        int $tahunAjaran,
        int $rombel
    ): JsonResponse {
        return response()->json(
            $this->service->byRombel(
                $tahunAjaran,
                $rombel
            )
        );
    }
}