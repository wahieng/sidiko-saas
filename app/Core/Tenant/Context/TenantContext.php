<?php

namespace App\Core\Tenant\Context;

use App\Core\Tenant\Models\Tenant;
use App\Core\Identity\Models\User;

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
    public function setFromUser(User $user): void
    {
        if (! $user->tenant_id) {
            $this->tenant = null;

            return;
        }

        $tenant = Tenant::find($user->tenant_id);

        if (! $tenant) {
            abort(403, 'Tenant tidak ditemukan.');
        }

        if (! $tenant->is_active) {
            abort(403, 'Tenant tidak aktif.');
        }

        $this->tenant = $tenant;
    }

    /**
     * Ambil tenant aktif.
     */
    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * Pastikan tenant aktif tersedia.
     */
    public function require(): Tenant
    {
        if (! $this->tenant) {
            abort(403, 'Tenant context tidak tersedia.');
        }

        return $this->tenant;
    }

    /**
     * Cek apakah context memiliki tenant.
     */
    public function has(): bool
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