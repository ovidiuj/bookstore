<?php

namespace App\TransferObjects\Search;


class PaginationTransfer
{
    protected ?int $page;

    protected ?int $lastPage;

    protected ?int $maxPerPage = 10;

    protected ?int $totalResults;

    protected ?int $previousPage;

    protected ?int $nextPage;

    protected ?int $firstPage;

    protected ?int $pagesCount;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): PaginationTransfer
    {
        $this->page = $page;

        return $this;
    }

    public function getLastPage(): ?int
    {
        return $this->lastPage;
    }

    public function setLastPage(?int $lastPage): void
    {
        $this->lastPage = $lastPage;
    }

    public function getMaxPerPage(): ?int
    {
        return $this->maxPerPage;
    }

    public function setMaxPerPage(?int $maxPerPage): PaginationTransfer
    {
        $this->maxPerPage = $maxPerPage;

        return $this;
    }

    public function getTotalResults(): ?int
    {
        return $this->totalResults;
    }

    public function setTotalResults(?int $totalResults): void
    {
        $this->totalResults = $totalResults;
    }

    public function getPreviousPage(): ?int
    {
        return $this->previousPage;
    }

    public function setPreviousPage(?int $previousPage): void
    {
        $this->previousPage = $previousPage;
    }

    public function getNextPage(): ?int
    {
        return $this->nextPage;
    }

    public function setNextPage(?int $nextPage): void
    {
        $this->nextPage = $nextPage;
    }

    public function getFirstPage(): ?int
    {
        return $this->firstPage;
    }

    public function setFirstPage(?int $firstPage): void
    {
        $this->firstPage = $firstPage;
    }

    public function getPagesCount(): ?int
    {
        return $this->pagesCount;
    }

    public function setPagesCount(?int $pagesCount): void
    {
        $this->pagesCount = $pagesCount;
    }
}
