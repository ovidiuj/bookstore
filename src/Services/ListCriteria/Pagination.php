<?php

namespace App\Services\ListCriteria;

use App\TransferObjects\Search\FilterTransfer;
use App\TransferObjects\Search\PaginationTransfer;
use Symfony\Component\HttpFoundation\Request;

class Pagination
{
    public function createPaginationTransfer(FilterTransfer $filterTransfer): PaginationTransfer
    {
        return (new PaginationTransfer())
            ->setPage($filterTransfer->getOffset() / $filterTransfer->getLimit() + 1)
            ->setMaxPerPage($filterTransfer->getLimit());
    }
}
