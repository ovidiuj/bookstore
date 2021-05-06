<?php

namespace App\Repository;

use App\Entity\Book;
use App\Mapper\BookMapperInterface;
use App\TransferObjects\Response\Book\BooksCollectionTransfer;
use App\TransferObjects\Response\CollectionTransferInterface;
use App\TransferObjects\Search\CriteriaFilterTransfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository implements BookRepositoryInterface
{
    use QueryBuilderTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function findAllAvailableBooks(
        CriteriaFilterTransfer $criteriaFilterTransfer,
        BookMapperInterface $bookMapper
    ): CollectionTransferInterface
    {
        $query = $this->buildQueryFromCriteria($this->createQueryBuilder('q'), $criteriaFilterTransfer);
        $query
            ->andWhere('q.status = :status')
            ->setParameter('status', Book::STATUS_PUBLIC);

        $results = $query->getQuery()
            ->getResult();

        $booksCollectionTransfer = (new BooksCollectionTransfer())->setBooks([])->setPagination();

        if (count($results) > 0) {
            foreach ($results as $result) {
                $booksCollectionTransfer->addBook($bookMapper->mapBookEntityToBookResponseTransfer($result));
            }

            $pagination = $this->getPagination($query, $criteriaFilterTransfer);
            $booksCollectionTransfer->setPagination($pagination);
        }

        return $booksCollectionTransfer;
    }

    public function findOneByUuid(string $value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.uuid = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function saveBook(Book $book): void
    {
        $this->getEntityManager()->persist($book);
        $this->getEntityManager()->flush();
    }

    public function deleteBook(Book $book): void
    {
        $this->getEntityManager()->remove($book);
        $this->getEntityManager()->flush();
    }
}
