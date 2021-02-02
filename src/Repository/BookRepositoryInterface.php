<?php


namespace App\Repository;


use App\Mapper\BookMapperInterface;
use App\TransferObjects\Response\CollectionTransferInterface;
use App\TransferObjects\Search\CriteriaFilterTransfer;

interface BookRepositoryInterface
{
    public function findAllAvailableBooks(
        CriteriaFilterTransfer $criteriaFilterTransfer,
        BookMapperInterface $bookMapper
    ): CollectionTransferInterface;
}