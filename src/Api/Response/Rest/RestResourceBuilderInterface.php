<?php

namespace App\Api\Response\Rest;


use App\TransferObjects\Response\ResponseTransferInterface;

interface RestResourceBuilderInterface
{

    public function createRestResource(
        string $type,
        ?string $id = null,
        ?ResponseTransferInterface $attributeTransfer = null
    ): RestResourceInterface;


    public function createRestResponse(): RestResponseInterface;
}
