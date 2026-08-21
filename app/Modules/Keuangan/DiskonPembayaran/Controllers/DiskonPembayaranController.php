<?php

namespace App\Modules\Keuangan\DiskonPembayaran\Controllers;

use App\Modules\Keuangan\DiskonPembayaran\Models\DiskonPembayaran;
use App\Modules\Keuangan\DiskonPembayaran\Requests\StoreDiskonPembayaranRequest;
use App\Modules\Keuangan\DiskonPembayaran\Requests\UpdateDiskonPembayaranRequest;
use App\Modules\Keuangan\DiskonPembayaran\Services\DiskonPembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiskonPembayaranController
{
    public function __construct(
        protected DiskonPembayaranService $service
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->getAll()
        );
    }

    public function show(
        DiskonPembayaran $diskonPembayaran
    ): JsonResponse {
        return response()->json(
            $this->service->find($diskonPembayaran->id)
        );
    }

    public function store(
        StoreDiskonPembayaranRequest $request
    ): JsonResponse {
        $diskonPembayaran = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Diskon pembayaran berhasil ditambahkan.',
            'data' => $diskonPembayaran->load([
                'siswaTahun',
                'tarifPembayaran',
            ]),
        ], 201);
    }

    public function update(
        UpdateDiskonPembayaranRequest $request,
        DiskonPembayaran $diskonPembayaran
    ): JsonResponse {
        $diskonPembayaran = $this->service->update(
            $diskonPembayaran,
            $request->validated()
        );

        return response()->json([
            'message' => 'Diskon pembayaran berhasil diperbarui.',
            'data' => $diskonPembayaran,
        ]);
    }

    public function destroy(
        DiskonPembayaran $diskonPembayaran
    ): JsonResponse {
        $this->service->delete($diskonPembayaran);

        return response()->json([
            'message' => 'Diskon pembayaran berhasil dihapus.',
        ]);
    }

    public function toggleStatus(
        DiskonPembayaran $diskonPembayaran
    ): JsonResponse {
        $diskonPembayaran = $this->service->toggleStatus(
            $diskonPembayaran
        );

        return response()->json([
            'message' => 'Status diskon pembayaran berhasil diubah.',
            'data' => $diskonPembayaran,
        ]);
    }

    public function bySiswaTahun(
        int $siswaTahun
    ): JsonResponse {
        return response()->json(
            $this->service->getBySiswaTahun($siswaTahun)
        );
    }

    public function activeBySiswaTahun(
        int $siswaTahun
    ): JsonResponse {
        return response()->json(
            $this->service->getActiveBySiswaTahun($siswaTahun)
        );
    }
}