<?php

namespace App\Core\Pagination\Resources;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PaginationResource
{
    /**
     * Transform paginator menjadi response pagination standar SIDIKO.
     */
    public static function make(LengthAwarePaginator $paginator): array
    {
        return [
            'data' => $paginator->items(),

            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
                'next_page' => $paginator->hasMorePages()
                    ? $paginator->currentPage() + 1
                    : null,
                'previous_page' => $paginator->currentPage() > 1
                    ? $paginator->currentPage() - 1
                    : null,
            ],
        ];
    }
}