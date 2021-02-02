<?php

namespace App\Config;

interface ConfigInterface
{
    public function getDefaultSortField(): string;

    public function getDefaultSortDirection(): string;

    public function getSortableFields(): array;

    public function getSearchableFields(): array;
}
