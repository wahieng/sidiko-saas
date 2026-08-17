<?php

namespace App\Core\Subscription\Controllers;

use App\Core\Subscription\Models\Langganan;
use App\Core\Subscription\Models\PaketLangganan;
use App\Core\Subscription\Services\SubscriptionService;
use App\Core\Tenant\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController
{
    public function __construct(
        protected SubscriptionService $service
    ) {
    }

    /**
     * Daftar seluruh subscription.
     */
    public function index(): JsonResponse
    {
        $langganan = Langganan::query()
            ->with([
                'tenant',
                'paket',
            ])
            ->latest('id')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $langganan,
        ]);
    }

    /**
     * Detail subscription.
     */
    public function show(Langganan $langganan): JsonResponse
    {
        $langganan->load([
            'tenant',
            'paket',
        ]);

        return response()->json([
            'success' => true,
            'data' => $langganan,
        ]);
    }

    /**
     * Membuat subscription trial.
     */
    public function buatTrial(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => [
                'required',
                'integer',
                'exists:tenants,id',
            ],

            'paket_langganan_id' => [
                'required',
                'integer',
                'exists:paket_langganan,id',
            ],

            'hari_trial' => [
                'nullable',
                'integer',
                'min:1',
                'max:365',
            ],
        ]);

        $tenant = Tenant::findOrFail(
            $validated['tenant_id']
        );

        $paket = PaketLangganan::findOrFail(
            $validated['paket_langganan_id']
        );

        $langganan = $this->service->buatTrial(
            $tenant,
            $paket,
            $validated['hari_trial'] ?? 14
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription trial berhasil dibuat.',
            'data' => $langganan->load([
                'tenant',
                'paket',
            ]),
        ], 201);
    }

    /**
     * Mengaktifkan subscription.
     */
    public function aktifkan(
        Langganan $langganan
    ): JsonResponse {
        $langganan = $this->service->aktifkan(
            $langganan
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription berhasil diaktifkan.',
            'data' => $langganan,
        ]);
    }

    /**
     * Membatalkan subscription.
     */
    public function batalkan(
        Langganan $langganan
    ): JsonResponse {
        $langganan = $this->service->batalkan(
            $langganan
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription berhasil dibatalkan.',
            'data' => $langganan,
        ]);
    }

    /**
     * Menangguhkan subscription.
     */
    public function suspend(
        Langganan $langganan
    ): JsonResponse {
        $langganan = $this->service->suspend(
            $langganan
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription berhasil disuspend.',
            'data' => $langganan,
        ]);
    }

    /**
     * Menandai subscription sebagai expired.
     */
    public function expire(
        Langganan $langganan
    ): JsonResponse {
        $langganan = $this->service->expire(
            $langganan
        );

        return response()->json([
            'success' => true,
            'message' => 'Subscription telah expired.',
            'data' => $langganan,
        ]);
    }
}