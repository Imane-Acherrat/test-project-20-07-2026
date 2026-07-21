<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait PaginatesResponses
{
    /**
     * Convert a LengthAwarePaginator into the project's standard
     * { data, pagination } response shape. $mapper transforms each item.
     */
    protected function paginated(LengthAwarePaginator $paginator, callable $mapper): array
    {
        return [
            'data' => $paginator->getCollection()->map($mapper)->values(),
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
                'totalItems' => $paginator->total(),
                'totalPages' => $paginator->lastPage(),
                'hasNextPage' => $paginator->hasMorePages(),
                'hasPreviousPage' => $paginator->currentPage() > 1,
            ],
        ];
    }

    /**
     * Resolve and clamp page/limit query params consistently across endpoints.
     */
    protected function paginationParams(int $defaultLimit = 10, int $maxLimit = 50): array
    {
        $page = max(1, (int) request()->query('page', 1));
        $limit = (int) request()->query('limit', $defaultLimit);

        if ($limit < 1) {
            $limit = $defaultLimit;
        }
        $limit = min($limit, $maxLimit);

        return [$page, $limit];
    }
}
