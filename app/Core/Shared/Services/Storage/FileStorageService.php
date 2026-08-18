<?php

namespace App\Core\Shared\Services\Storage;

use App\Core\Shared\Contracts\FileStorageInterface;
use App\Core\Shared\Exceptions\StorageException;
use Illuminate\Support\Facades\Storage;
use Throwable;

class FileStorageService implements FileStorageInterface
{
    /**
     * Disk storage yang digunakan.
     */
    protected string $disk = 'public';

    public function put(
        string $path,
        mixed $contents
    ): string {
        try {
            $success = Storage::disk($this->disk)->put(
                $path,
                $contents
            );

            if (! $success) {
                throw new StorageException(
                    "Gagal menyimpan file: {$path}"
                );
            }

            return $path;
        } catch (StorageException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new StorageException(
                "Gagal menyimpan file: {$path}",
                0,
                $e
            );
        }
    }

    public function get(string $path): string
    {
        try {
            if (! $this->exists($path)) {
                throw new StorageException(
                    "File tidak ditemukan: {$path}"
                );
            }

            return Storage::disk($this->disk)->get($path);
        } catch (StorageException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new StorageException(
                "Gagal membaca file: {$path}",
                0,
                $e
            );
        }
    }

    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    public function delete(string $path): bool
    {
        try {
            if (! $this->exists($path)) {
                return false;
            }

            return Storage::disk($this->disk)->delete($path);
        } catch (Throwable $e) {
            throw new StorageException(
                "Gagal menghapus file: {$path}",
                0,
                $e
            );
        }
    }

    public function url(string $path): string
    {
        try {
            if (! $this->exists($path)) {
                throw new StorageException(
                    "File tidak ditemukan: {$path}"
                );
            }

            return Storage::disk($this->disk)->url($path);
        } catch (StorageException $e) {
            throw $e;
        } catch (Throwable $e) {
            throw new StorageException(
                "Gagal mendapatkan URL file: {$path}",
                0,
                $e
            );
        }
    }
}