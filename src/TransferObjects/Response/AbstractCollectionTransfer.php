<?php

namespace App\TransferObjects\Response;


use App\TransferObjects\Search\PaginationTransfer;

abstract class AbstractCollectionTransfer implements CollectionTransferInterface
{
    private ?PaginationTransfer $pagination;

    abstract public function getTransferObjects(): array;

    public function getPagination(): ?PaginationTransfer
    {
        return $this->pagination;
    }

    public function setPagination(?PaginationTransfer $pagination = null): CollectionTransferInterface
    {
        $this->pagination = $pagination;
        return $this;
    }
}
