<?php


namespace App\Services;


use App\Config\BookConfig;
use App\Entity\Book;
use App\Mapper\BookMapperInterface;
use App\Repository\BookRepositoryInterface;
use App\Services\ListCriteria\ListCriteriaServiceInterface;
use App\TransferObjects\Request\Book\BookRequestTransfer;
use App\TransferObjects\Request\Book\EditBookRequestTransfer;
use App\TransferObjects\Response\Book\BookTransfer;
use App\TransferObjects\Response\CollectionTransferInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class BookService implements BookServiceInterface
{
    private BookRepositoryInterface $bookRepository;
    private ListCriteriaServiceInterface $listCriteriaService;
    private BookMapperInterface $bookMapper;
    private EntityManagerInterface $entityManager;

    public function __construct(
        BookRepositoryInterface $bookRepository,
        ListCriteriaServiceInterface $listCriteriaService,
        BookMapperInterface $bookMapper,
        EntityManagerInterface $entityManager
    )
    {
        $this->bookRepository = $bookRepository;
        $this->listCriteriaService = $listCriteriaService;
        $this->bookMapper = $bookMapper;
        $this->entityManager = $entityManager;
    }

    public function findAllAvailableBooks(Request $request): CollectionTransferInterface
    {
        $criteriaFilterTransfer = $this->listCriteriaService->createCriteriaFilterTransfer($request, new BookConfig());
        return $this->bookRepository->findAllAvailableBooks($criteriaFilterTransfer, $this->bookMapper);
    }

    public function addBook(BookRequestTransfer $bookRequestTransfer): BookTransfer
    {
        $bookEntity = $this->bookMapper->mapBookEntityFromBookRequestTransfer(new Book(), $bookRequestTransfer);

        $this->entityManager->persist($bookEntity);
        $this->entityManager->flush();

        return $this->bookMapper->mapBookEntityToBookResponseTransfer($bookEntity);
    }

    public function editBook(string $id, EditBookRequestTransfer $editBookRequestTransfer): BookTransfer
    {
        $bookEntity = $this->bookRepository->findOneByUuid($id);
        if ($bookEntity === null) {
            throw new ConflictHttpException('A book with provided id does not exist.');
        }

        $bookEntity = $this->bookMapper->mapBookEntityFromEditBookRequestTransfer($bookEntity, $editBookRequestTransfer);

        $this->entityManager->persist($bookEntity);
        $this->entityManager->flush();

        return $this->bookMapper->mapBookEntityToBookResponseTransfer($bookEntity);
    }

    public function deleteBook(string $uuid): ?BookTransfer
    {
        $bookEntity = $this->bookRepository->findOneByUuid($uuid);
        if ($bookEntity === null) {
            throw new ConflictHttpException('A book with provided id does not exist.');
        }

        if ($bookEntity->getStatus() !== Book::STATUS_NOT_PUBLIC) {
            throw new ConflictHttpException('A public book can\'t be deleted.');
        }

        $this->entityManager->remove($bookEntity);
        $this->entityManager->flush();

        return null;
    }
}