<?php

namespace App\Core\Tenant\Context;

use App\Core\Tenant\Models\Tenant;

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
     * Ambil ID tenant aktif.
     */
    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    /**
     * Pastikan tenant tersedia.
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