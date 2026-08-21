<?php

namespace App\Modules\Keuangan\Tagihan\Controllers;

use App\Modules\Keuangan\Tagihan\Requests\StoreTagihanRequest;
use App\Modules\Keuangan\Tagihan\Requests\UpdateTagihanRequest;
use App\Modules\Keuangan\Tagihan\Services\TagihanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagihanController
{
    public function __construct(
        protected TagihanService $tagihanService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->tagihanService->getAll(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->tagihanService->find($id),
        ]);
    }

    public function store(StoreTagihanRequest $request): JsonResponse
    {
        $tagihan = $this->tagihanService->create(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibuat.',
            'data' => $tagihan,
        ], 201);
    }

    public function update(
        UpdateTagihanRequest $request,
        int $id
    ): JsonResponse {
        $tagihan = $this->tagihanService->find($id);

        $tagihan = $this->tagihanService->update(
            $tagihan,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil diperbarui.',
            'data' => $tagihan,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $tagihan = $this->tagihanService->find($id);

        $tagihan = $this->tagihanService->cancel($tagihan);

        return response()->json([
            'success' => true,
            'message' => 'Tagihan berhasil dibatalkan.',
            'data' => $tagihan,
        ]);
    }
}