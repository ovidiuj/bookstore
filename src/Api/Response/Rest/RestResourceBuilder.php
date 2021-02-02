<?php

namespace App\Api\Response\Rest;


use App\TransferObjects\Response\ResponseTransferInterface;

class RestResourceBuilder implements RestResourceBuilderInterface
{

    public function createRestResource(
        string $type,
        ?string $id = null,
        ?ResponseTransferInterface $attributeTransfer = null
    ): RestResourceInterface {

        return new RestResource($type, $id, $attributeTransfer);
    }


    public function createRestResponse(): RestResponseInterface
    {
        return new RestResponse();
    }
}
