<?php

use App\Core\Tenant\Context\TenantContext;
use App\Core\Tenant\Models\Tenant;
use App\Modules\Siswa\Models\Siswa;

$tenant = Tenant::where('code', 'DEMO')->firstOrFail();

app(TenantContext::class)->set($tenant);

$siswa = Siswa::create([
    'nis' => '99999',
    'nama' => 'Test Tenant Siswa',
    'jenis_kelamin' => 'L',
]);

dump($siswa->toArray());
