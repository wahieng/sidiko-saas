<?php

namespace App\Core\Shared\Contracts;

interface FileStorageInterface
{
    /**
     * Simpan file dan kembalikan path file.
     */
    public function put(
        string $path,
        mixed $contents
    ): string;

    /**
     * Ambil isi file.
     */
    public function get(string $path): string;

    /**
     * Cek apakah file tersedia.
     */
    public function exists(string $path): bool;

    /**
     * Hapus file.
     */
    public function delete(string $path): bool;

    /**
     * Ambil URL file.
     */
    public function url(string $path): string;
}