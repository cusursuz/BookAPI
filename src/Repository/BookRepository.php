<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public const ITEMS_PER_PAGE = 100;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function getPaginated(int $page = 1): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('b');
        $queryBuilder->setFirstResult(self::ITEMS_PER_PAGE * ($page - 1));
        $queryBuilder->setMaxResults(self::ITEMS_PER_PAGE);
        $paginator = new Paginator($queryBuilder->getQuery());

        return $paginator;
    }
}
