<?php
namespace App\Services;

use App\TransferObjects\Request\Book\BookRequestTransfer;
use App\TransferObjects\Request\Book\EditBookRequestTransfer;
use App\TransferObjects\Response\Book\BookTransfer;
use App\TransferObjects\Response\CollectionTransferInterface;
use Symfony\Component\HttpFoundation\Request;

interface BookServiceInterface
{
    public function findAllAvailableBooks(Request $request): CollectionTransferInterface;

    public function addBook(BookRequestTransfer $bookRequestTransfer): BookTransfer;

    public function editBook(string $id, EditBookRequestTransfer $editBookRequestTransfer): BookTransfer;

    public function deleteBook(string $uuid): ?BookTransfer;

    public function getBook(string $uuid): ?BookTransfer;
}