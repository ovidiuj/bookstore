<?php

namespace App\TransferObjects\Search;

class FilterTransfer
{
    protected ?int $limit;

    protected ?int $offset;

    protected ?string $orderBy;

    protected ?string $orderDirection = "DESC";

    protected $searchableFields = [];

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function setLimit(?int $limit): FilterTransfer
    {
        $this->limit = $limit;

        return $this;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setOffset(?int $offset): FilterTransfer
    {
        $this->offset = $offset;

        return $this;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function setOrderBy(?string $orderBy): FilterTransfer
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function setOrderDirection(?string $orderDirection): FilterTransfer
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }

    public function getSearchableFields(): array
    {
        return $this->searchableFields;
    }

    public function setSearchableFields(array $searchableFields): FilterTransfer
    {
        $this->searchableFields = $searchableFields;

        return $this;
    }
}
