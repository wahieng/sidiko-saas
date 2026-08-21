<?php

namespace App\Core\Access\Console\Commands;

use App\Core\Access\Services\PermissionSyncService;
use Illuminate\Console\Command;

class PermissionSyncCommand extends Command
{
    protected $signature = 'permission:sync';

    protected $description = 'Sinkronisasi permission dari route SIDIKO';

    public function handle(
        PermissionSyncService $service
    ): int {
        $result = $service->sync();

        $this->newLine();

        $this->info('Permission sync selesai.');

        $this->table(
            [
                'Metric',
                'Jumlah',
            ],
            [
                [
                    'Routes scanned',
                    $result['routes_scanned'],
                ],
                [
                    'Permission routes',
                    $result['permission_routes'],
                ],
                [
                    'Permissions found',
                    $result['permissions_found'],
                ],
                [
                    'Created',
                    $result['created'],
                ],
                [
                    'Existing',
                    $result['existing'],
                ],
                [
                    'Invalid',
                    $result['invalid'],
                ],
            ]
        );

        if ($result['invalid'] > 0) {
            $this->newLine();

            $this->error('Route invalid yang ditemukan:');

            $this->table(
                [
                    'Method',
                    'URI',
                    'Route Name',
                ],
                collect($result['invalid_routes'])
                    ->map(fn ($route) => [
                        $route['methods'],
                        $route['uri'],
                        $route['name'],
                    ])
                    ->values()
                    ->all()
            );

            return self::FAILURE;
        }

        $this->info(
            'Semua route yang menggunakan permission middleware berhasil disinkronkan.'
        );

        return self::SUCCESS;
    }
}