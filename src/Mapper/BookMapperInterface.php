<?php
namespace App\Mapper;

use App\Entity\Book;
use App\TransferObjects\Request\Book\BookRequestTransfer;
use App\TransferObjects\Request\Book\EditBookRequestTransfer;
use App\TransferObjects\Response\Book\BookTransfer;

interface BookMapperInterface
{
    public function mapBookEntityToBookResponseTransfer(Book $book): BookTransfer;

    public function mapBookEntityFromBookRequestTransfer(
        Book $book,
        BookRequestTransfer $bookRequestTransfer
    ): Book;

    public function mapBookEntityFromEditBookRequestTransfer(
        Book $book,
        EditBookRequestTransfer $editBookRequestTransfer
    ): Book;
}