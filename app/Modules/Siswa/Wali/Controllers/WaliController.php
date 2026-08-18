<?php

namespace App\Modules\Siswa\Wali\Controllers;

use App\Modules\Siswa\Siswa\Models\Siswa;
use App\Modules\Siswa\Wali\Models\Wali;
use App\Modules\Siswa\Wali\Requests\StoreWaliRequest;
use App\Modules\Siswa\Wali\Requests\UpdateWaliRequest;
use App\Modules\Siswa\Wali\Services\WaliService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class WaliController
{
    public function __construct(
        protected WaliService $service
    ) {
    }

    /**
     * Daftar wali milik siswa.
     */
    public function index(Siswa $siswa): JsonResponse
    {
        return response()->json(
            $this->service->bySiswa($siswa->id)
        );
    }

    /**
     * Simpan wali untuk siswa.
     */
    public function store(
        StoreWaliRequest $request,
        Siswa $siswa
    ): JsonResponse {
        $data = $request->validated();

        // Siswa ditentukan dari URL, bukan dari input user.
        $data['siswa_id'] = $siswa->id;

        $wali = $this->service->create($data);

        return response()->json(
            $wali->load('siswa'),
            201
        );
    }

    /**
     * Detail wali.
     */
    public function show(Wali $wali): JsonResponse
    {
        return response()->json(
            $wali->load('siswa')
        );
    }

    /**
     * Update wali.
     */
    public function update(
        UpdateWaliRequest $request,
        Wali $wali
    ): JsonResponse {
        $wali = $this->service->update(
            $wali,
            $request->validated()
        );

        return response()->json(
            $wali->load('siswa')
        );
    }

    /**
     * Hapus wali.
     */
    public function destroy(Wali $wali): Response
    {
        $this->service->delete($wali);

        return response()->noContent();
    }
}