<?php

namespace App\Modules\Siswa\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Siswa\Models\Siswa;
use App\Modules\Siswa\Requests\StoreSiswaRequest;
use App\Modules\Siswa\Requests\UpdateSiswaRequest;
use App\Modules\Siswa\Services\SiswaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function __construct(
        protected SiswaService $service
    ) {
    }

    /**
     * Daftar siswa.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 20);

        $siswa = $this->service->paginate($perPage);

        return response()->json($siswa);
    }

    /**
     * Detail siswa.
     */
    public function show(Siswa $siswa): JsonResponse
    {
        return response()->json([
            'data' => $siswa,
        ]);
    }

    /**
     * Simpan siswa baru.
     */
    public function store(StoreSiswaRequest $request): JsonResponse
    {
        $siswa = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Data siswa berhasil dibuat.',
            'data' => $siswa,
        ], 201);
    }

    /**
     * Update siswa.
     */
    public function update(
        UpdateSiswaRequest $request,
        Siswa $siswa
    ): JsonResponse {
        $siswa = $this->service->update(
            $siswa,
            $request->validated()
        );

        return response()->json([
            'message' => 'Data siswa berhasil diperbarui.',
            'data' => $siswa,
        ]);
    }

    /**
     * Hapus siswa.
     */
    public function destroy(Siswa $siswa): JsonResponse
    {
        $this->service->delete($siswa);

        return response()->json([
            'message' => 'Data siswa berhasil dihapus.',
        ]);
    }
}