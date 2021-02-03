<?php


namespace App\Services\HttpClientService;

use App\Entity\Book;

class ClientBookMapper implements ClientBookMapperInterface
{
    public function getBookParams(Book $book): string
    {
        $bookParams = [];
        $bookParams['title'] = $book->getTitle();
        $bookParams['cover'] = $book->getCover();
        $bookParams['author'] = $book->getAuthor();
        $bookParams['description'] = $book->getDescription();
        $bookParams['status'] = $book->getStatus();
        return json_encode($bookParams);
    }


    public function createBookEntity(array $bookParams): Book
    {
        $bookEntity = new Book();
        $bookEntity->setTitle($bookParams['data'][0]['attributes']['title']);
        $bookEntity->setCover($bookParams['data'][0]['attributes']['cover']);
        $bookEntity->setAuthor($bookParams['data'][0]['attributes']['author']);
        $bookEntity->setDescription($bookParams['data'][0]['attributes']['description']);
        $bookEntity->setStatus($bookParams['data'][0]['attributes']['status']);

        return $bookEntity;
    }
}