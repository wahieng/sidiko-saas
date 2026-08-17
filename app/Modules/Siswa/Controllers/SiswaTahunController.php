<?php

namespace App\Modules\Siswa\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Siswa\Models\SiswaTahun;
use App\Modules\Siswa\Requests\StoreSiswaTahunRequest;
use App\Modules\Siswa\Requests\UpdateSiswaTahunRequest;
use App\Modules\Siswa\Services\SiswaTahunService;
use Illuminate\Http\JsonResponse;

class SiswaTahunController extends Controller
{
    public function __construct(
        protected SiswaTahunService $service
    ) {
    }

    /**
     * Tampilkan seluruh data siswa tahun.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->all()
        );
    }

    /**
     * Tampilkan satu data siswa tahun.
     */
    public function show(
        SiswaTahun $siswaTahun
    ): JsonResponse {
        return response()->json(
            $siswaTahun->load([
                'siswa',
                'tahunAjaran',
                'kelompokRombel.rombel',
            ])
        );
    }

    /**
     * Simpan data siswa tahun.
     */
    public function store(
        StoreSiswaTahunRequest $request
    ): JsonResponse {
        $siswaTahun = $this->service->create(
            $request->validated()
        );

        return response()->json(
            $siswaTahun,
            201
        );
    }

    /**
     * Update data siswa tahun.
     */
    public function update(
        UpdateSiswaTahunRequest $request,
        SiswaTahun $siswaTahun
    ): JsonResponse {
        $siswaTahun = $this->service->update(
            $siswaTahun,
            $request->validated()
        );

        return response()->json(
            $siswaTahun
        );
    }

    /**
     * Hapus data siswa tahun.
     */
    public function destroy(
        SiswaTahun $siswaTahun
    ): JsonResponse {
        $siswaTahun->delete();

        return response()->json([
            'message' => 'Data siswa tahun berhasil dihapus.',
        ]);
    }
}