<?php

namespace App\Modules\Siswa\Services;

use App\Core\Shared\Services\Storage\FileStorageService;
use App\Core\Shared\Support\Storage\StoragePathHelper;
use App\Core\Tenant\Context\TenantContext;
use App\Modules\Siswa\Models\DokumenSiswa;
use App\Modules\Siswa\Models\Siswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class DokumenSiswaService
{
    public function __construct(
        protected FileStorageService $storage,
        protected StoragePathHelper $pathHelper,
        protected TenantContext $tenantContext,
    ) {
    }

    /**
     * Simpan dokumen siswa.
     *
     * Aturan:
     * - Siswa harus berada pada tenant aktif.
     * - Satu jenis dokumen hanya boleh memiliki satu file.
     * - Upload baru akan menggantikan dokumen lama.
     * - File lama dihapus dari storage.
     * - Record lama dihapus dari database.
     */
    public function store(
        Siswa $siswa,
        UploadedFile $file,
        string $jenisDokumen,
        ?string $keterangan = null
    ): DokumenSiswa {
        $tenant = $this->tenantContext->get();

        if (! $tenant) {
            throw new RuntimeException(
                'Tenant context tidak tersedia.'
            );
        }

        if ((int) $siswa->tenant_id !== (int) $tenant->id) {
            throw new RuntimeException(
                'Siswa bukan milik tenant aktif.'
            );
        }

        $jenisDokumen = trim($jenisDokumen);

        if ($jenisDokumen === '') {
            throw new RuntimeException(
                'Jenis dokumen wajib diisi.'
            );
        }

        $originalName = $file->getClientOriginalName();

        $extension = strtolower(
            $file->getClientOriginalExtension()
        );

        $filename = Str::uuid() . '.' . $extension;

        $directory = $this->pathHelper->siswaDokumen();

        $path = $directory . '/' . $filename;

        /*
         * Cari dokumen lama dengan jenis yang sama.
         */
        $dokumenLama = DokumenSiswa::query()
            ->where('tenant_id', $tenant->id)
            ->where('siswa_id', $siswa->id)
            ->where('jenis_dokumen', $jenisDokumen)
            ->first();

        /*
         * Simpan file baru terlebih dahulu.
         */
        $this->storage->put(
            $path,
            $file->get()
        );

        try {
            $dokumenBaru = DB::transaction(function () use (
                $tenant,
                $siswa,
                $jenisDokumen,
                $originalName,
                $filename,
                $path,
                $file,
                $keterangan,
                $dokumenLama
            ) {
                /*
                 * Hapus record dokumen lama.
                 */
                if ($dokumenLama) {
                    $dokumenLama->delete();
                }

                return DokumenSiswa::create([
                    'tenant_id' => $tenant->id,
                    'siswa_id' => $siswa->id,
                    'jenis_dokumen' => $jenisDokumen,
                    'nama_file' => $filename,
                    'nama_asli' => $originalName,
                    'path' => $path,
                    'disk' => 'public',
                    'mime_type' => $file->getMimeType(),
                    'ukuran' => $file->getSize(),
                    'keterangan' => $keterangan,
                ]);
            });

            /*
             * Setelah database berhasil, baru hapus file lama.
             */
            if ($dokumenLama && $dokumenLama->path !== $path) {
                $this->storage->delete(
                    $dokumenLama->path
                );
            }

            return $dokumenBaru;
        } catch (Throwable $e) {
            /*
             * Jika database gagal, file baru jangan ditinggalkan.
             */
            $this->storage->delete($path);

            throw $e;
        }
    }
}