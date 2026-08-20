<?php

namespace Tests\Feature\Routing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Regression guard for route-name consistency.
 *
 * App\Core\Access\Middleware\PermissionMiddleware::resolveResource()
 * requires every route protected by the `permission` middleware to follow the
 * `module.resource.action` convention (exactly 3 dot-segments). Any route name
 * with a different segment count makes resolveResource() return null and the
 * middleware aborts with 403 for every user — regardless of roles.
 *
 * This test locks in the canonical, sibling-consistent route names for the
 * Siswa and Keuangan modules so that regression is caught immediately.
 */
class RouteNameConsistencyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Canonical, 3-segment route names that MUST be registered after the
     * naming-consistency fixes.
     */
    private array $canonicalNames = [
        // Keuangan: TarifPembayaran must carry the `keuangan.` module prefix,
        // like its siblings jenis-pembayaran and diskon-pembayaran.
        'keuangan.tarif-pembayaran.index',
        'keuangan.tarif-pembayaran.store',
        'keuangan.tarif-pembayaran.show',
        'keuangan.tarif-pembayaran.update',
        'keuangan.tarif-pembayaran.destroy',

        // Siswa core resource now nests as module.resource.action.
        'siswa.siswa.index',
        'siswa.siswa.store',
        'siswa.siswa.show',
        'siswa.siswa.update',
        'siswa.siswa.destroy',

        // Siswa sub-modules nest under the `siswa.` domain, matching
        // siswa.dokumen.* (and Wali is now URL-nested under {siswa}).
        'siswa.wali.index',
        'siswa.wali.store',
        'siswa.wali.show',
        'siswa.wali.update',
        'siswa.wali.destroy',

        'siswa.orang-tua.index',
        'siswa.orang-tua.store',
        'siswa.orang-tua.show',
        'siswa.orang-tua.update',
        'siswa.orang-tua.destroy',

        'siswa.siswa-tahun.index',
        'siswa.siswa-tahun.store',
        'siswa.siswa-tahun.show',
        'siswa.siswa-tahun.update',
        'siswa.siswa-tahun.destroy',

        // 4-segment outlier collapsed to 3 segments so the middleware can
        // resolve its resource.
        'keuangan.diskon-pembayaran.siswa-aktif',
    ];

    public function test_canonical_module_route_names_are_registered(): void
    {
        foreach ($this->canonicalNames as $name) {
            $this->assertTrue(
                Route::has($name),
                "Expected route name [{$name}] is not registered."
            );
        }
    }

    public function test_canonical_module_route_names_have_three_segments(): void
    {
        foreach ($this->canonicalNames as $name) {
            $route = Route::getRoutes()->getByName($name);

            $this->assertNotNull($route, "Route [{$name}] should exist.");

            $segments = explode('.', $route->getName());

            $this->assertCount(
                3,
                $segments,
                "Route name [{$name}] must use the module.resource.action"
                . " convention (exactly 3 dot-segments); got"
                . ' "' . $route->getName() . '".'
            );
        }
    }

    public function test_legacy_inconsistent_route_names_are_removed(): void
    {
        $legacyNames = [
            // TarifPembayaran used a bare 2-segment name with no module prefix.
            'tarif-pembayaran.index',
            'tarif-pembayaran.store',
            'tarif-pembayaran.show',
            'tarif-pembayaran.update',
            'tarif-pembayaran.destroy',

            // Siswa sub-modules were flat 2-segment names.
            'wali.index',
            'orang-tua.index',
            'siswa_tahun.index',

            // Siswa core used a bare 2-segment name.
            'siswa.index',
            'siswa.show',

            // DiskonPembayaran had a 4-segment name (would also 403).
            'keuangan.diskon-pembayaran.siswa.aktif',
        ];

        foreach ($legacyNames as $name) {
            $this->assertFalse(
                Route::has($name),
                "Legacy route name [{$name}] should no longer be registered."
            );
        }
    }

    public function test_wali_routes_are_nested_under_siswa_in_uri(): void
    {
        // WaliController::index(Siswa $siswa) and store(..., Siswa $siswa)
        // require the {siswa} parameter; the URI must therefore be nested.
        $uri = Route::getRoutes()
            ->getByName('siswa.wali.show')
            ->uri();

        $this->assertStringContainsString('{siswa}/wali', $uri);
    }

    public function test_siswa_dokumen_routes_remain_consistent(): void
    {
        // Guard: pre-existing, already-correct sibling must stay 3-segment.
        foreach (['siswa.dokumen.store', 'siswa.dokumen.show'] as $name) {
            $this->assertTrue(Route::has($name), "Route [{$name}] missing.");
        }
    }
}
