<?php

namespace App\Api\Response;


use App\TransferObjects\Response\CollectionTransferInterface;
use App\TransferObjects\Response\ResponseTransferInterface;
use Symfony\Component\HttpFoundation\Request;

interface ApiResponseInterface
{
    public function buildJsonResponse(?ResponseTransferInterface $transfer, string $type);

    public function buildJsonResponseFromArray(
        ?CollectionTransferInterface $collectionTransfer,
        string $type,
        Request $request
    );

    public function buildJsonResponseFromSimpleArray(array $data);
}
