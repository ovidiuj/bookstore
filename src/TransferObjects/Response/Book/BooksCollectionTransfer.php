<?php


namespace App\TransferObjects\Response\Book;



use App\TransferObjects\Response\AbstractCollectionTransfer;
use App\TransferObjects\Response\CollectionTransferInterface;

class BooksCollectionTransfer extends AbstractCollectionTransfer
{
    private array $books;

    public function getMandants(): array
    {
        return $this->books;
    }

    public function setBooks(array $books): CollectionTransferInterface
    {
        $this->books = $books;

        return $this;
    }

    public function addBook(BookTransfer $bookTransfer): void
    {
        $this->books[] = $bookTransfer;
    }

    public function getTransferObjects(): array
    {
        return $this->getMandants();
    }
}