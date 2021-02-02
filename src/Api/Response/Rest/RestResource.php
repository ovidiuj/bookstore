<?php

namespace App\Api\Response\Rest;


use App\TransferObjects\Response\ResponseTransferInterface;

class RestResource implements RestResourceInterface
{

    protected ?string $id;

    protected string $type;

    protected ?ResponseTransferInterface $attributes;


    public function __construct(string $type, ?string $id = null, ?ResponseTransferInterface $attributes = null)
    {
        $this->type       = $type;
        $this->id         = $id;
        $this->attributes = $attributes;
    }


    public function getId(): ?string
    {
        return $this->id;
    }


    public function getType(): string
    {
        return $this->type;
    }


    public function getAttributes(): ?ResponseTransferInterface
    {
        return $this->attributes;
    }


    public function toArray(): array
    {

        $response = [
            RestResourceInterface::RESOURCE_TYPE => $this->type,
            RestResourceInterface::RESOURCE_ID   => $this->id,
        ];

        if ($this->attributes) {
            $response[RestResourceInterface::RESOURCE_ATTRIBUTES] = $this->transformTransferToArray($this->attributes);
        }

        return $response;
    }


    private function transformTransferToArray(ResponseTransferInterface $transfer): array
    {
        $reflectionClass   = new \ReflectionClass(get_class($transfer));
        $objectProprieties = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $objectProprieties[$property->getName()] = $property->getValue($transfer);
            $property->setAccessible(false);
        }

        return $objectProprieties;
    }
}
