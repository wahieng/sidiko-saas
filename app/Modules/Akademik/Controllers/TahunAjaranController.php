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

    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->all()
        );
    }

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

    public function show(
        TahunAjaran $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $tahunAjaran->load('semesters')
        );
    }

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

    public function destroy(
        TahunAjaran $tahunAjaran
    ): JsonResponse {
        $this->service->delete($tahunAjaran);

        return response()->json([
            'message' => 'Tahun ajaran berhasil dihapus.',
        ]);
    }

    public function semester(
        TahunAjaran $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $tahunAjaran->semesters()->get()
        );
    }
}