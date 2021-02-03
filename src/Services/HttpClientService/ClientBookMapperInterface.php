<?php


namespace App\Services\HttpClientService;

use App\Entity\Book;

interface ClientBookMapperInterface
{
    public function getBookParams(Book $book): string;
    public function createBookEntity(array $bookParams): Book;
}