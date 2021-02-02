<?php

namespace App\TransferObjects\Search;


use App\TransferObjects\Request\RequestTransferInterface;
use App\TransferObjects\Search\FilterTransfer;
use App\TransferObjects\Search\PaginationTransfer;

class CriteriaFilterTransfer implements RequestTransferInterface
{
    public const PARAMETER_SEARCH = 'q';
    public const PARAMETER_PAGE = 'page';
    public const PARAMETER_PAGE_LIMIT = 'limit';
    public const PARAMETER_PAGE_OFFSET = 'offset';
    public const PARAMETER_SORT = 'sort';

    protected ?string $searchTerm;

    protected FilterTransfer $filter;

    protected PaginationTransfer $pagination;

    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(?string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }

    public function getFilter(): FilterTransfer
    {
        return $this->filter;
    }

    public function setFilter(FilterTransfer $filter): void
    {
        $this->filter = $filter;
    }

    public function getPagination(): PaginationTransfer
    {
        return $this->pagination;
    }

    public function setPagination(PaginationTransfer $pagination): void
    {
        $this->pagination = $pagination;
    }
}
