<?php

namespace App\Modules\Keuangan\TarifPembayaran\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Keuangan\TarifPembayaran\Requests\StoreTarifPembayaranRequest;
use App\Modules\Keuangan\TarifPembayaran\Requests\UpdateTarifPembayaranRequest;
use App\Modules\Keuangan\TarifPembayaran\Services\TarifPembayaranService;
use Illuminate\Http\JsonResponse;

class TarifPembayaranController extends Controller
{
    public function __construct(
        protected TarifPembayaranService $service
    ) {
    }

    /**
     * Menampilkan seluruh tarif pembayaran.
     */
    public function index(): JsonResponse
    {
        $data = $this->service->getAll();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Menampilkan detail tarif pembayaran.
     */
    public function show(int $id): JsonResponse
    {
        $data = $this->service->find($id);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Menyimpan tarif pembayaran baru.
     */
    public function store(
        StoreTarifPembayaranRequest $request
    ): JsonResponse {
        $data = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Tarif pembayaran berhasil ditambahkan.',
            'data' => $data,
        ], 201);
    }

    /**
     * Mengubah tarif pembayaran.
     */
    public function update(
        UpdateTarifPembayaranRequest $request,
        int $id
    ): JsonResponse {
        $data = $this->service->update(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Tarif pembayaran berhasil diperbarui.',
            'data' => $data,
        ]);
    }

    /**
     * Menghapus tarif pembayaran.
     */
    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Tarif pembayaran berhasil dihapus.',
        ]);
    }
}