<?php

namespace App\Api\Response\Rest;

use App\TransferObjects\Search\PaginationTransfer;
use Symfony\Component\HttpFoundation\Request;

interface RestResponseInterface
{
    public const RESPONSE_ERRORS = 'errors';
    public const RESPONSE_DATA   = 'data';
    public const RESPONSE_PAGINATION   = 'pagination';

    public function addError(string $error);

    public function getErrors(): array;

    public function addResource(RestResourceInterface $restResource): RestResponseInterface;

    public function getResources(): array;

    public function getPagination(): array;

    public function buildPagination(PaginationTransfer $paginationTransfer, Request $request): void;
}
