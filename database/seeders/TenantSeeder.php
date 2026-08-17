<?php

namespace Database\Seeders;

use App\Core\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Tenant::updateOrCreate(
            [
                'code' => 'DEMO',
            ],
            [
                'name' => 'Demo School',
                'slug' => 'demo-school',
                'email' => 'admin@demo-school.test',
                'phone' => '0800000000',
                'address' => 'Alamat Demo',
                'logo' => null,
                'is_active' => true,
            ]
        );
    }
}