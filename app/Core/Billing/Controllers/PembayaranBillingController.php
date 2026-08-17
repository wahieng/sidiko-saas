<?php

namespace App\Core\Billing\Controllers;

use App\Core\Billing\Models\TagihanBilling;
use App\Core\Billing\Requests\PaymentRequest;
use App\Core\Billing\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PembayaranBillingController
{
    public function __construct(
        protected PaymentService $paymentService
    ) {
    }

    /**
     * Daftar pembayaran sebuah tagihan.
     */
    public function index(
        TagihanBilling $tagihan
    ): JsonResponse {
        $pembayaran = $tagihan
            ->pembayaran()
            ->latest('tanggal_pembayaran')
            ->paginate(15);

        return response()->json($pembayaran);
    }

    /**
     * Proses pembayaran tagihan.
     */
    public function store(
        PaymentRequest $request,
        TagihanBilling $tagihan
    ): JsonResponse {
        $pembayaran = $this->paymentService->pay(
            $tagihan,
            $request->validated()
        );

        return response()->json([
            'message' => 'Pembayaran berhasil diproses.',
            'data' => $pembayaran,
        ], 201);
    }
}