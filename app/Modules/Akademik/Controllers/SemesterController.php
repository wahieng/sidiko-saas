<?php

namespace App\Modules\Akademik\Controllers;

use App\Modules\Akademik\Models\Semester;
use App\Modules\Akademik\Requests\SemesterRequest;
use App\Modules\Akademik\Services\SemesterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SemesterController
{
    public function __construct(
        protected SemesterService $service
    ) {
    }

    /**
     * Daftar semua semester.
     */
    public function index(): JsonResponse
    {
        return response()->json(
            $this->service->all()
        );
    }

    /**
     * Daftar semester berdasarkan tahun ajaran.
     */
    public function byTahunAjaran(
        int $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $this->service->byTahunAjaran(
                $tahunAjaran
            )
        );
    }

    /**
     * Simpan semester.
     */
    public function store(
        SemesterRequest $request
    ): JsonResponse {
        $semester = $this->service->create(
            $request->validated()
        );

        return response()->json(
            $semester->load('tahunAjaran'),
            201
        );
    }

    /**
     * Detail semester.
     */
    public function show(
        Semester $semester
    ): JsonResponse {
        return response()->json(
            $semester->load('tahunAjaran')
        );
    }

    /**
     * Update semester.
     */
    public function update(
        SemesterRequest $request,
        Semester $semester
    ): JsonResponse {
        $semester = $this->service->update(
            $semester,
            $request->validated()
        );

        return response()->json(
            $semester->load('tahunAjaran')
        );
    }

    /**
     * Nonaktifkan semester.
     */
    public function destroy(
        Semester $semester
    ): JsonResponse {
        $this->service->nonaktifkan(
            $semester
        );

        return response()->json([
            'message' => 'Semester berhasil dinonaktifkan.',
        ]);
    }

    /**
     * Aktifkan semester.
     */
    public function aktifkan(
        Semester $semester
    ): JsonResponse {
        return response()->json(
            $this->service->aktifkan(
                $semester
            )
        );
    }

    /**
     * Ambil semester aktif.
     */
    public function aktif(): JsonResponse
    {
        return response()->json(
            $this->service->aktif()
        );
    }

    /**
     * Ambil semester aktif berdasarkan tahun ajaran.
     */
    public function aktifByTahunAjaran(
        int $tahunAjaran
    ): JsonResponse {
        return response()->json(
            $this->service->aktifByTahunAjaran(
                $tahunAjaran
            )
        );
    }
}