<?php

namespace App\Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\Rombel;
use App\Modules\Akademik\Requests\RombelRequest;
use App\Modules\Akademik\Services\RombelService;
use Illuminate\Http\JsonResponse;

class RombelController
{
    public function __construct(
        protected RombelService $service
    ) {
    }

    /**
     * Daftar rombel aktif.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->allAktif()
        );
    }

    /**
     * Simpan rombel.
     */
    public function store(
        RombelRequest $request
    ): JsonResponse {
        $rombel = $this->service->create(
            $request->validated()
        );

        return response()->json(
            $rombel,
            201
        );
    }

    /**
     * Detail rombel.
     */
    public function show(
        Rombel $rombel
    ): JsonResponse {
        return response()->json(
            $rombel->load('kelompokRombel')
        );
    }

    /**
     * Update rombel.
     */
    public function update(
        RombelRequest $request,
        Rombel $rombel
    ): JsonResponse {
        $rombel = $this->service->update(
            $rombel,
            $request->validated()
        );

        return response()->json(
            $rombel
        );
    }

    /**
     * Nonaktifkan rombel.
     */
    public function destroy(
        Rombel $rombel
    ): JsonResponse {
        $this->service->nonaktifkan(
            $rombel
        );

        return response()->json([
            'message' => 'Rombel berhasil dinonaktifkan.',
        ]);
    }
}