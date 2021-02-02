<?php

namespace App\Repository;

use App\TransferObjects\Search\CriteriaFilterTransfer;
use App\TransferObjects\Search\PaginationTransfer;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait QueryBuilderTrait
{
    public function buildQueryFromCriteria(QueryBuilder $query, CriteriaFilterTransfer $criteriaFilterTransfer): QueryBuilder
    {
        $searchTerm = $criteriaFilterTransfer->getSearchTerm();
        $filter = $criteriaFilterTransfer->getFilter();
        $searchableFields = $filter->getSearchableFields();

        if ($searchTerm && count($searchableFields) > 0) {
            foreach ($searchableFields as $field) {
                $query->orWhere("q.$field LIKE :searchTerm");
            }
            $query->setParameter('searchTerm', '%' . $searchTerm . '%');
        }


        if ($filter->getOffset() !== null) {
            $query->setFirstResult($filter->getOffset());
        }

        if ($filter->getLimit()) {
            $query->setMaxResults($filter->getLimit());
        }

        if ($filter->getOrderBy()) {
            $query->orderBy('q.' . $filter->getOrderBy(), $filter->getOrderDirection());
        }

        return $query;
    }

    public function getPagination(QueryBuilder $query, CriteriaFilterTransfer $criteriaFilterTransfer): PaginationTransfer
    {
        $paginator = new Paginator($query);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $criteriaFilterTransfer->getPagination()->getMaxPerPage());
        $criteriaFilterTransfer->getPagination()->setTotalResults($totalItems);
        $criteriaFilterTransfer->getPagination()->setPagesCount($pagesCount);

        return $criteriaFilterTransfer->getPagination();
    }
}
