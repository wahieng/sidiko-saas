<?php

namespace App\Core\Shared\Support\Storage;

use App\Core\Tenant\Context\TenantContext;
use Symfony\Component\HttpKernel\Exception\HttpException;

class StoragePathHelper
{
    public function __construct(
        protected TenantContext $tenantContext
    ) {
    }

    /**
     * Path dasar tenant aktif.
     */
    public function tenant(): string
    {
        $tenant = $this->tenantContext->get();

        if (! $tenant) {
            throw new HttpException(
                403,
                'Tenant context tidak tersedia.'
            );
        }

        return 'tenants/' . $tenant->id;
    }

    /**
     * Path berdasarkan module.
     */
    public function module(string $module): string
    {
        return $this->tenant() . '/' . trim($module, '/');
    }

    /**
     * Path siswa.
     */
    public function siswa(string $folder = ''): string
    {
        return $this->module('siswa')
            . ($folder ? '/' . trim($folder, '/') : '');
    }

    /**
     * Path foto siswa.
     */
    public function siswaFoto(): string
    {
        return $this->siswa('foto');
    }

    /**
     * Path dokumen siswa.
     */
    public function siswaDokumen(): string
    {
        return $this->siswa('dokumen');
    }

    /**
     * Path keuangan.
     */
    public function keuangan(string $folder = ''): string
    {
        return $this->module('keuangan')
            . ($folder ? '/' . trim($folder, '/') : '');
    }

    /**
     * Path umum tenant.
     */
    public function umum(string $folder = ''): string
    {
        return $this->module('umum')
            . ($folder ? '/' . trim($folder, '/') : '');
    }
}