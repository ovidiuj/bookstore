<?php

namespace App\Services\ListCriteria;


use App\Config\ConfigInterface;
use App\TransferObjects\Search\CriteriaFilterTransfer;
use Symfony\Component\HttpFoundation\Request;

class ListCriteriaService implements ListCriteriaServiceInterface
{
    protected Filter $filter;
    protected Pagination $pagination;

    public function __construct(Filter $filter, Pagination $pagination)
    {
        $this->filter = $filter;
        $this->pagination = $pagination;
    }

    public function createCriteriaFilterTransfer(Request $request, ConfigInterface $config): CriteriaFilterTransfer
    {
        $criteriaFilterTransfer = new CriteriaFilterTransfer();
        $criteriaFilterTransfer->setSearchTerm($this->getRequestParameter($request));
        $filterTransfer = $this->filter->createFilterTransfer($request, $config);
        $criteriaFilterTransfer->setFilter($filterTransfer);
        $criteriaFilterTransfer->setPagination($this->pagination->createPaginationTransfer($filterTransfer));

        return $criteriaFilterTransfer;
    }

    private function getRequestParameter(Request $request): ?string
    {
        return $request->query->get(CriteriaFilterTransfer::PARAMETER_SEARCH);
    }
}
