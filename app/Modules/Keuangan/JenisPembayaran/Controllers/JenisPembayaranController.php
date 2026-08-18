<?php

namespace App\Modules\Keuangan\JenisPembayaran\Controllers;

use App\Modules\Keuangan\JenisPembayaran\Requests\StoreJenisPembayaranRequest;
use App\Modules\Keuangan\JenisPembayaran\Requests\UpdateJenisPembayaranRequest;
use App\Modules\Keuangan\JenisPembayaran\Services\JenisPembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JenisPembayaranController
{
    public function __construct(
        protected JenisPembayaranService $service
    ) {
    }

    /**
     * Menampilkan semua jenis pembayaran.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->all(),
        ]);
    }

    /**
     * Menampilkan jenis pembayaran aktif.
     */
    public function active(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->active(),
        ]);
    }

    /**
     * Menampilkan detail jenis pembayaran.
     */
    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->service->find($id),
        ]);
    }

    /**
     * Menyimpan jenis pembayaran baru.
     */
    public function store(
        StoreJenisPembayaranRequest $request
    ): JsonResponse {
        $jenisPembayaran = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Jenis pembayaran berhasil dibuat.',
            'data' => $jenisPembayaran,
        ], 201);
    }

    /**
     * Memperbarui jenis pembayaran.
     */
    public function update(
        UpdateJenisPembayaranRequest $request,
        int $id
    ): JsonResponse {
        $jenisPembayaran = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Jenis pembayaran berhasil diperbarui.',
            'data' => $jenisPembayaran,
        ]);
    }

    /**
     * Menghapus jenis pembayaran.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Jenis pembayaran berhasil dihapus.',
        ]);
    }
}