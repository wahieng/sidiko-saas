<?php

namespace App\Core\Pagination\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class PaginationService
{
    /**
     * Default jumlah data per halaman.
     */
    private const DEFAULT_PER_PAGE = 15;

    /**
     * Maksimal jumlah data per halaman.
     */
    private const MAX_PER_PAGE = 100;

    /**
     * Paginate Eloquent query.
     */
    public function paginate(
        Builder $query,
        int $page = 1,
        int $perPage = self::DEFAULT_PER_PAGE
    ): LengthAwarePaginator {
        $page = max(1, $page);

        $perPage = max(
            1,
            min($perPage, self::MAX_PER_PAGE)
        );

        return $query->paginate(
            perPage: $perPage,
            page: $page
        );
    }
}