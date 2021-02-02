<?php

namespace App\Services\ListCriteria;


use App\Config\ConfigInterface;
use App\TransferObjects\Search\CriteriaFilterTransfer;
use Symfony\Component\HttpFoundation\Request;

interface ListCriteriaServiceInterface
{
    public function createCriteriaFilterTransfer(Request $request, ConfigInterface $config): CriteriaFilterTransfer;
}
