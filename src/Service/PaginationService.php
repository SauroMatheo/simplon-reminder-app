<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;

class PaginationService
{
    public const ITEMS_PER_PAGE = 9;

    public function paginate(QueryBuilder $queryBuilder, Request $request, int $limit = self::ITEMS_PER_PAGE): array
    {
        $page = max(1, $request->query->getInt('page', 1));

        $paginator = new Paginator($queryBuilder);

        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        $totalItems = $paginator->count();
        $totalPages = ceil($totalItems / $limit);

        if ($page > $totalPages && $totalPages > 0) {
            $page = $totalPages;
        }

        return [
            'items' => iterator_to_array($paginator),
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'itemsPerPage' => $limit,
            'totalItems' => $totalItems,
            'hasNextPage' => $page < $totalPages,
            'hasPreviousPage' => $page > 1,
        ];
    }
}

