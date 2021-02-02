<?php

namespace App\Config;

abstract class AbstractConfig implements ConfigInterface
{
    protected string $defaultSortField = 'createdAt';
    protected string $defaultSortDirection = 'DESC';
    protected array $sortableFields = [];
    protected array $searchableFields = [];

    public function getDefaultSortField(): string
    {
        return $this->defaultSortField;
    }

    public function getDefaultSortDirection(): string
    {
        return $this->defaultSortDirection;
    }

    public function getSortableFields(): array
    {
        return $this->sortableFields;
    }

    public function getSearchableFields(): array
    {
        return $this->searchableFields;
    }
}
