<?php

namespace App\Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\KelompokRombel;
use App\Modules\Akademik\Requests\KelompokRombelRequest;
use App\Modules\Akademik\Services\KelompokRombelService;
use Illuminate\Http\JsonResponse;

class KelompokRombelController
{
    public function __construct(
        protected KelompokRombelService $service
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->byTahunAjaran(
                request()->integer('tahun_ajaran_id')
            )
        );
    }

    public function store(
        KelompokRombelRequest $request
    ): JsonResponse {
        $kelompokRombel = $this->service->create(
            $request->validated()
        );

        return response()->json(
            $kelompokRombel->load('rombel'),
            201
        );
    }

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

    public function update(
        KelompokRombelRequest $request,
        KelompokRombel $kelompokRombel
    ): JsonResponse {
        $kelompokRombel = $this->service->update(
            $kelompokRombel,
            $request->validated()
        );

        return response()->json(
            $kelompokRombel->load('rombel')
        );
    }

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

    public function byTahunAjaran(
        int $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $this->service->aktifByTahunAjaran(
                $tahunAjaran
            )
        );
    }

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