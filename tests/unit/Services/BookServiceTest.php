<?php


namespace App\Tests\Unit\Services;


use App\Config\BookConfig;
use App\Entity\Book;
use App\Mapper\BookMapper;
use App\Repository\BookRepository;
use App\Services\BookService;
use App\Services\ListCriteria\ListCriteriaService;
use App\TransferObjects\Request\Book\BookRequestTransfer;
use App\TransferObjects\Request\Book\EditBookRequestTransfer;
use App\TransferObjects\Response\Book\BooksCollectionTransfer;
use App\TransferObjects\Response\Book\BookTransfer;
use App\TransferObjects\Search\CriteriaFilterTransfer;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class BookServiceTest extends TestCase
{
    const BOOK_TITLE = '1984';
    const BOOK_COVER = 'Winston Smith';
    const BOOK_AUTHOR = 'George Orwell';
    const BOOK_DESCRIPTION = 'an omniscient government with an agenda';
    const BOOK_ID = '47086b91-3346-4fa0-b299-7c056c2a1bae';

    private $listCriteriaService;
    private $bookRepository;
    private $bookMapper;
    private $entityManager;
    private $bookService;

    public function setUp(): void
    {
        $this->listCriteriaService = $this->createMock(ListCriteriaService::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookMapper = $this->createMock(BookMapper::class);
        $this->entityManager = $this->createMock(EntityManager::class);

        $this->bookService = new BookService(
            $this->bookRepository,
            $this->listCriteriaService,
            $this->bookMapper,
            $this->entityManager
        );
    }

    public function testFindAllAvailableBooks()
    {
        $request = Request::create('http://bookstore.test/api/v1/books');
        $criteriaFilterTransfer = new CriteriaFilterTransfer();

        $this->listCriteriaService->expects($this->once())
            ->method('createCriteriaFilterTransfer')
            ->with($request, new BookConfig())
            ->willReturn($criteriaFilterTransfer);

        $this->bookRepository->expects($this->once())
            ->method('findAllAvailableBooks')
            ->with($criteriaFilterTransfer, $this->bookMapper)
            ->willReturn(new BooksCollectionTransfer());

        $books = $this->bookService->findAllAvailableBooks($request);
        $this->assertInstanceOf(BooksCollectionTransfer::class, $books);
    }


    public function testAddBook()
    {
        $bookRequestTransfer= $this->getBookRequestTransfer();

        $expectedBookTransfer = $this->bookService->addBook($bookRequestTransfer);
        $this->assertInstanceOf(BookTransfer::class, $expectedBookTransfer);
    }

    public function testEditBook()
    {
        $bookRequestTransfer= $this->getEditBookRequestTransfer();
        $bookEntity = $this->getBookEntity();

        $this->bookRepository->expects($this->once())
            ->method('findOneByUuid')
            ->with(self::BOOK_ID)
            ->willReturn($bookEntity);

        $this->bookMapper->expects($this->once())
            ->method('mapBookEntityFromEditBookRequestTransfer')
            ->with($bookEntity, $bookRequestTransfer)
            ->willReturn($bookEntity);

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($bookEntity);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $expectedBookTransfer = $this->bookService->editBook(self::BOOK_ID, $bookRequestTransfer);
        $this->assertInstanceOf(BookTransfer::class, $expectedBookTransfer);
    }

    public function testEditNotFoundBook()
    {
        $bookRequestTransfer= $this->getEditBookRequestTransfer();

        $this->bookRepository->expects($this->once())
            ->method('findOneByUuid')
            ->with(self::BOOK_ID)
            ->willReturn(null);

        $this->expectException(ConflictHttpException::class);
        $this->expectExceptionMessage('A book with provided id does not exist.');

        $this->bookService->editBook(self::BOOK_ID, $bookRequestTransfer);
    }

    public function testDeleteBook()
    {
        $bookEntity = $this->getBookEntity();
        $bookEntity->setStatus(Book::STATUS_NOT_PUBLIC);

        $this->bookRepository->expects($this->once())
            ->method('findOneByUuid')
            ->with(self::BOOK_ID)
            ->willReturn($bookEntity);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($bookEntity);

        $this->entityManager->expects($this->once())
            ->method('flush');

        $expectedResponse = $this->bookService->deleteBook(self::BOOK_ID);
        $this->assertEquals(null, $expectedResponse);
    }

    public function testDeleteAPublicBook()
    {
        $bookEntity = $this->getBookEntity();

        $this->bookRepository->expects($this->once())
            ->method('findOneByUuid')
            ->with(self::BOOK_ID)
            ->willReturn($bookEntity);

        $this->expectException(ConflictHttpException::class);
        $this->expectExceptionMessage('A public book can\'t be deleted');

        $this->bookService->deleteBook(self::BOOK_ID);
    }

    public function testDeleteANotFoundBook()
    {
        $this->bookRepository->expects($this->once())
            ->method('findOneByUuid')
            ->with(self::BOOK_ID)
            ->willReturn(null);

        $this->expectException(ConflictHttpException::class);
        $this->expectExceptionMessage('A book with provided id does not exist.');

        $this->bookService->deleteBook(self::BOOK_ID);
    }

    public function testGetBook()
    {
        $bookEntity = $this->getBookEntity();

        $this->bookRepository->expects($this->once())
            ->method('findOneByUuid')
            ->with(self::BOOK_ID)
            ->willReturn($bookEntity);

        $this->bookMapper->expects($this->once())
            ->method('mapBookEntityToBookResponseTransfer')
            ->with($bookEntity)
            ->willReturn($this->getBookTransfer());


        $expectedResponse = $this->bookService->getBook(self::BOOK_ID);
        $this->assertInstanceOf(BookTransfer::class, $expectedResponse);
    }

    public function testGetANotFoundBook()
    {
        $this->bookRepository->expects($this->once())
            ->method('findOneByUuid')
            ->with(self::BOOK_ID)
            ->willReturn(null);

        $this->expectException(ConflictHttpException::class);
        $this->expectExceptionMessage('A book with provided id does not exist.');

        $this->bookService->getBook(self::BOOK_ID);
    }

    public function testGetANotPublicBook()
    {
        $bookEntity = $this->getBookEntity();
        $bookEntity->setStatus(Book::STATUS_NOT_PUBLIC);
        $this->bookRepository->expects($this->once())
            ->method('findOneByUuid')
            ->with(self::BOOK_ID)
            ->willReturn($bookEntity);

        $this->expectException(ConflictHttpException::class);
        $this->expectExceptionMessage('A not-public book can\'t be fetched.');

        $expectedResponse = $this->bookService->getBook(self::BOOK_ID);
        $this->assertInstanceOf(BookTransfer::class, $expectedResponse);
    }


    public function getBookRequestTransfer(): BookRequestTransfer
    {
        $bookRequestTransfer = new BookRequestTransfer();
        $bookRequestTransfer->setStatus(Book::STATUS_PUBLIC);
        $bookRequestTransfer->setTitle(self::BOOK_TITLE);
        $bookRequestTransfer->setCover(self::BOOK_COVER);
        $bookRequestTransfer->setAuthor(self::BOOK_AUTHOR);
        $bookRequestTransfer->setDescription(self::BOOK_DESCRIPTION);

        return $bookRequestTransfer;
    }

    private function getEditBookRequestTransfer(): EditBookRequestTransfer
    {
        $bookRequestTransfer = new EditBookRequestTransfer();
        $bookRequestTransfer->setStatus(Book::STATUS_PUBLIC);
        $bookRequestTransfer->setTitle(self::BOOK_TITLE);
        $bookRequestTransfer->setCover(self::BOOK_COVER);
        $bookRequestTransfer->setAuthor(self::BOOK_AUTHOR);
        $bookRequestTransfer->setDescription(self::BOOK_DESCRIPTION);

        return $bookRequestTransfer;
    }

    private function getBookEntity(): Book
    {
        $bookEntity = new Book();
        $bookEntity->setStatus(Book::STATUS_PUBLIC);
        $bookEntity->setTitle(self::BOOK_TITLE);
        $bookEntity->setCover(self::BOOK_COVER);
        $bookEntity->setAuthor(self::BOOK_AUTHOR);
        $bookEntity->setTitle(self::BOOK_DESCRIPTION);

        return $bookEntity;
    }

    private function getBookTransfer(): BookTransfer
    {
        $bookTransfer = new BookTransfer();
        $bookTransfer->setStatus(Book::STATUS_PUBLIC);
        $bookTransfer->setTitle(self::BOOK_TITLE);
        $bookTransfer->setCover(self::BOOK_COVER);
        $bookTransfer->setAuthor(self::BOOK_AUTHOR);
        $bookTransfer->setDescription(self::BOOK_DESCRIPTION);

        return $bookTransfer;
    }

}