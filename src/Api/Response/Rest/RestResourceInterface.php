<?php

namespace App\Api\Response\Rest;


use App\TransferObjects\Response\ResponseTransferInterface;

interface RestResourceInterface
{
    public const RESOURCE_DATA       = 'data';
    public const RESOURCE_TYPE       = 'type';
    public const RESOURCE_ID         = 'id';
    public const RESOURCE_ATTRIBUTES = 'attributes';


    public function getId(): ?string;


    public function getType(): string;


    public function getAttributes(): ?ResponseTransferInterface;


    public function toArray(): array;
}
