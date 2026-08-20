<?php

namespace App\Core\Tenant\Context;

use App\Core\Tenant\Models\Tenant;
use RuntimeException;

class TenantContext
{
    protected ?Tenant $tenant = null;

    /**
     * Set tenant aktif.
     */
    public function set(Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    /**
     * Set tenant berdasarkan user.
     */
    public function setFromUser($user): void
    {
        if (! $user || ! $user->tenant_id) {
            $this->clear();

            return;
        }

        $tenant = Tenant::query()
            ->whereKey($user->tenant_id)
            ->where('is_active', true)
            ->first();

        if (! $tenant) {
            $this->clear();

            return;
        }

        $this->set($tenant);
    }

    /**
     * Ambil tenant aktif.
     */
    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * Ambil tenant aktif.
     *
     * Digunakan ketika tenant WAJIB tersedia.
     */
    public function require(): Tenant
    {
        if (! $this->tenant) {
            throw new RuntimeException(
                'Tenant context belum tersedia.'
            );
        }

        return $this->tenant;
    }

    /**
     * Ambil ID tenant aktif.
     */
    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    /**
     * Periksa apakah tenant tersedia.
     */
    public function check(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Hapus tenant aktif.
     */
    public function clear(): void
    {
        $this->tenant = null;
    }
}