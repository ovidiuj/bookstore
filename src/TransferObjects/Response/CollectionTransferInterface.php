<?php

namespace App\TransferObjects\Response;


use App\TransferObjects\Search\PaginationTransfer;

interface CollectionTransferInterface
{
    public function getTransferObjects(): array;

    public function getPagination(): ?PaginationTransfer;

    public function setPagination(?PaginationTransfer $pagination = null): CollectionTransferInterface;
}
