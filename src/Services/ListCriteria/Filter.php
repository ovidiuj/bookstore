<?php

namespace App\Services\ListCriteria;

use App\Config\ConfigInterface;
use App\TransferObjects\Search\CriteriaFilterTransfer;
use App\TransferObjects\Search\FilterTransfer;
use Symfony\Component\HttpFoundation\Request;

class Filter
{
    private int $offset = 0;
    private int $limit = 15;

    public function createFilterTransfer(Request $request, ConfigInterface $config): FilterTransfer
    {
        $filterTransfer = new FilterTransfer();
        $page = $request->query->get(CriteriaFilterTransfer::PARAMETER_PAGE);
        if (is_array($page) && count($page) > 0) {
            $this->limit = isset($page[CriteriaFilterTransfer::PARAMETER_PAGE_LIMIT]) ? (int) $page[CriteriaFilterTransfer::PARAMETER_PAGE_LIMIT] : $this->limit;
            $this->offset = isset($page[CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET]) ? (int) $page[CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET] : 0;
        }
        $filterTransfer
            ->setOffset($this->offset)
            ->setLimit($this->limit);

        $filterTransfer = $this->setSearchableFields($filterTransfer, $config);

        return $this->applySortingToFilter($request, $config, $filterTransfer);
    }

    private function applySortingToFilter(
        Request $request,
        ConfigInterface $config,
        FilterTransfer $filterTransfer
    ): FilterTransfer {

        $sortField = $config->getDefaultSortField();
        $sortDirection = $config->getDefaultSortDirection();

        if ($sort = $request->query->get(CriteriaFilterTransfer::PARAMETER_SORT)) {
            [$field, $direction] = array_pad(explode('-', $sort), 2, null);

            if ($field && in_array($field, $config->getSortableFields())) {
                $sortField = $field ?? $sortField;
                $sortDirection = $direction ? strtoupper($direction) : $sortDirection;
            }
        }

        $filterTransfer
            ->setOrderBy($sortField)
            ->setOrderDirection($sortDirection);

        return $filterTransfer;
    }

    private function setSearchableFields(FilterTransfer $filterTransfer, ConfigInterface $config): FilterTransfer
    {
        return $filterTransfer->setSearchableFields($config->getSearchableFields());
    }
}
