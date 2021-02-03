<?php


namespace App\Mapper;


use App\Entity\Book;
use App\TransferObjects\Request\Book\BookRequestTransfer;
use App\TransferObjects\Request\Book\EditBookRequestTransfer;
use App\TransferObjects\Response\Book\BookTransfer;

class BookMapper implements BookMapperInterface
{
    public function mapBookEntityToBookResponseTransfer(Book $book): BookTransfer
    {
        $bookTransfer = new BookTransfer();
        $bookTransfer->setUuid($book->getUuid());
        $bookTransfer->setTitle($book->getTitle());
        $bookTransfer->setDescription($book->getDescription());
        $bookTransfer->setAuthor($book->getAuthor());
        $bookTransfer->setCover($book->getCover());
        $bookTransfer->setStatus($book->getStatus());
        $bookTransfer->setCreatedAt($book->getCreatedAt()->format('Y-m-d H:i:s'));

        return $bookTransfer;
    }

    public function mapBookEntityFromBookRequestTransfer(
        Book $book,
        BookRequestTransfer $bookRequestTransfer
    ): Book
    {
        $book->setTitle($bookRequestTransfer->getTitle());
        $book->setAuthor($bookRequestTransfer->getAuthor());
        $book->setCover($bookRequestTransfer->getCover());
        $book->setDescription($bookRequestTransfer->getDescription());
        $book->setStatus($bookRequestTransfer->getStatus());

        return $book;
    }

    public function mapBookEntityFromEditBookRequestTransfer(
        Book $book,
        EditBookRequestTransfer $editBookRequestTransfer
    ): Book
    {
        if($editBookRequestTransfer->getTitle()) {
            $book->setTitle($editBookRequestTransfer->getTitle());
        }

        if($editBookRequestTransfer->getAuthor()) {
            $book->setAuthor($editBookRequestTransfer->getAuthor());
        }

        if($editBookRequestTransfer->getCover()) {
            $book->setCover($editBookRequestTransfer->getCover());
        }

        if($editBookRequestTransfer->getDescription()) {
            $book->setDescription($editBookRequestTransfer->getDescription());
        }

        if($editBookRequestTransfer->getStatus()) {
            $book->setStatus($editBookRequestTransfer->getStatus());
        }

        return $book;
    }

}