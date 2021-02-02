<?php

namespace App\Api\Response\Rest;


use App\TransferObjects\Search\CriteriaFilterTransfer;
use App\TransferObjects\Search\PaginationTransfer;
use Symfony\Component\HttpFoundation\Request;

class RestResponse implements RestResponseInterface
{

    protected array $resources = [];

    protected array $errors = [];

    protected array $pagination = [];

    protected array $urlParams = [];

    protected string $basePath;


    public function addError(string $error): RestResponseInterface
    {
        $this->errors[RestResponseInterface::RESPONSE_ERRORS][] = $error;

        return $this;
    }


    public function getErrors(): array
    {
        return $this->errors;
    }


    public function addResource(RestResourceInterface $restResource): RestResponseInterface
    {
        $resource = $restResource->toArray();
        if (isset($resource[RestResourceInterface::RESOURCE_ATTRIBUTES]['uuid'])) {
            unset($resource[RestResourceInterface::RESOURCE_ATTRIBUTES]['uuid']);
        }
        $this->resources[RestResponseInterface::RESPONSE_DATA][] = $resource;

        return $this;
    }


    public function getResources(): array
    {
        return $this->resources;
    }

    public function getPagination(): array
    {
        return $this->pagination;
    }

    public function setPagination(array $pagination): void
    {
        $this->pagination[self::RESPONSE_PAGINATION] = $pagination;
    }

    public function buildPagination(PaginationTransfer $paginationTransfer, Request $request): void
    {
        $this->setBasePath($request);
        $this->pagination[self::RESPONSE_PAGINATION]['totalResult'] = $paginationTransfer->getTotalResults();
        $this->pagination[self::RESPONSE_PAGINATION]['firstPage'] = $this->getFirstPage($request);
        $this->pagination[self::RESPONSE_PAGINATION]['lastPage'] = $this->getLastPage($paginationTransfer);
        $this->pagination[self::RESPONSE_PAGINATION]['previousPage'] = $this->getPreviousPage($paginationTransfer, $request);
        $this->pagination[self::RESPONSE_PAGINATION]['nextPage'] = $this->getNextPage($paginationTransfer, $request);
        $this->pagination[self::RESPONSE_PAGINATION]['page'] = $paginationTransfer->getPage();
        $this->pagination[self::RESPONSE_PAGINATION]['pagesCount'] = $paginationTransfer->getPagesCount();
        $this->pagination[self::RESPONSE_PAGINATION]['maxPerPage'] = $paginationTransfer->getMaxPerPage();
    }

    private function getFirstPage(Request $request)
    {
        $searchParam = $request->query->get(CriteriaFilterTransfer::PARAMETER_SEARCH);
        $page = $request->query->get(CriteriaFilterTransfer::PARAMETER_PAGE);

        if ($searchParam) {
            $this->urlParams[CriteriaFilterTransfer::PARAMETER_SEARCH] = $searchParam;
        }

        if (isset($page[CriteriaFilterTransfer::PARAMETER_PAGE_LIMIT])) {
            $this->urlParams[CriteriaFilterTransfer::PARAMETER_PAGE][CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET] = 0;
            $this->urlParams[CriteriaFilterTransfer::PARAMETER_PAGE][CriteriaFilterTransfer::PARAMETER_PAGE_LIMIT] = $page[CriteriaFilterTransfer::PARAMETER_PAGE_LIMIT];
        }

        return urldecode($this->basePath . ($this->urlParams ? '?' . http_build_query($this->urlParams) : ''));
    }

    private function getLastPage(PaginationTransfer $paginationTransfer)
    {
        if ($paginationTransfer->getPagesCount() > 1) {
            $this->urlParams[CriteriaFilterTransfer::PARAMETER_PAGE][CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET] = (int)($paginationTransfer->getTotalResults() - $paginationTransfer->getMaxPerPage() + 1);
            $this->urlParams[CriteriaFilterTransfer::PARAMETER_PAGE][CriteriaFilterTransfer::PARAMETER_PAGE_LIMIT] = $paginationTransfer->getMaxPerPage();
            return urldecode($this->basePath . ($this->urlParams ? '?' . http_build_query($this->urlParams) : ''));
        }

        return null;
    }

    private function getNextPage(PaginationTransfer $paginationTransfer, Request $request): ?string
    {
        if ($paginationTransfer->getPagesCount() > 1 && $paginationTransfer->getPage() < $paginationTransfer->getPagesCount()) {
            $page = $request->query->get(CriteriaFilterTransfer::PARAMETER_PAGE);

            $this->urlParams[CriteriaFilterTransfer::PARAMETER_PAGE][CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET] = ($page[CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET] ?? 0) + $paginationTransfer->getMaxPerPage();
            $this->urlParams[CriteriaFilterTransfer::PARAMETER_PAGE][CriteriaFilterTransfer::PARAMETER_PAGE_LIMIT] = $paginationTransfer->getMaxPerPage();

            return urldecode($this->basePath . ($this->urlParams ? '?' . http_build_query($this->urlParams) : ''));
        }

        return null;
    }

    private function getPreviousPage(PaginationTransfer $paginationTransfer, Request $request): ?string
    {
        if ($paginationTransfer->getPagesCount() > 1) {
            $page = $request->query->get(CriteriaFilterTransfer::PARAMETER_PAGE);
            $this->urlParams[CriteriaFilterTransfer::PARAMETER_PAGE][CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET] = isset($page[CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET]) ? ($page[CriteriaFilterTransfer::PARAMETER_PAGE_OFFSET] - $paginationTransfer->getMaxPerPage()) : 0 ;
            $this->urlParams[CriteriaFilterTransfer::PARAMETER_PAGE][CriteriaFilterTransfer::PARAMETER_PAGE_LIMIT] = $paginationTransfer->getMaxPerPage();
            return urldecode($this->basePath . ($this->urlParams ? '?' . http_build_query($this->urlParams) : ''));
        }

        return null;
    }

    private function setBasePath(Request $request): void
    {
        $urlParams = parse_url($request->getUri());
        $this->basePath = $urlParams['path'];
    }
}
