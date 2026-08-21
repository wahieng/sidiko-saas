<?php

namespace App\Core\Billing\Controllers;

use App\Core\Billing\Requests\GenerateBillingRequest;
use App\Core\Billing\Services\BillingService;
use App\Core\Billing\Services\GenerateBillingService;
use App\Core\Subscription\Models\Langganan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillingController
{
    public function __construct(
        protected BillingService $billingService,
        protected GenerateBillingService $generateBillingService
    ) {
    }

    /**
     * Daftar tagihan Billing SaaS.
     */
    public function index(Request $request): JsonResponse
    {
        $tagihan = $this->billingService->paginate(
            $request->integer('page', 1),
            $request->integer('per_page', 15)
        );

        return response()->json($tagihan);
    }

    /**
     * Detail tagihan.
     */
    public function show(int $id): JsonResponse
    {
        $tagihan = $this->billingService->find($id);

        return response()->json($tagihan);
    }

    /**
     * Generate tagihan dari langganan.
     */
    public function generate(
        GenerateBillingRequest $request
    ): JsonResponse {
        $langganan = Langganan::findOrFail(
            $request->validated('langganan_id')
        );

        $tagihan = $this->generateBillingService->generate(
            $langganan
        );

        return response()->json([
            'message' => 'Tagihan berhasil dibuat.',
            'data' => $tagihan,
        ], 201);
    }
}