<?php

namespace App\Modules\Siswa\Controllers;

use App\Modules\Siswa\Models\OrangTua;
use App\Modules\Siswa\Requests\StoreOrangTuaRequest;
use App\Modules\Siswa\Requests\UpdateOrangTuaRequest;
use App\Modules\Siswa\Services\OrangTuaService;
use Illuminate\Http\JsonResponse;

class OrangTuaController
{
    public function __construct(
        protected OrangTuaService $service
    ) {
    }

    /**
     * Daftar semua orang tua.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->all()
        );
    }

    /**
     * Detail orang tua.
     */
    public function show(OrangTua $orangTua): JsonResponse
    {
        return response()->json(
            $this->service->find($orangTua->id)
        );
    }

    /**
     * Simpan orang tua.
     */
    public function store(
        StoreOrangTuaRequest $request
    ): JsonResponse {
        $orangTua = $this->service->create(
            $request->validated()
        );

        return response()->json(
            $orangTua->load('siswa'),
            201
        );
    }

    /**
     * Update orang tua.
     */
    public function update(
        UpdateOrangTuaRequest $request,
        OrangTua $orangTua
    ): JsonResponse {
        $orangTua = $this->service->update(
            $orangTua,
            $request->validated()
        );

        return response()->json(
            $orangTua->load('siswa')
        );
    }

    /**
     * Hapus orang tua.
     */
    public function destroy(
        OrangTua $orangTua
    ): JsonResponse {
        $this->service->delete($orangTua);

        return response()->json([
            'message' => 'Data orang tua berhasil dihapus.',
        ]);
    }
}