<?php


namespace App\Services\HttpClientService;


class HttpClientServiceException extends \Exception
{
    public function getStatusCode()
    {
        return $this->getCode();
    }
}