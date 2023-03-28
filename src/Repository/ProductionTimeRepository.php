<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\ProductionTime;
use App\Entity\Project;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductionTime>
 *
 * @method ProductionTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductionTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductionTime[]    findAll()
 * @method ProductionTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductionTimeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductionTime::class);
    }

    public function save(ProductionTime $entity, Employee $employee, bool $flush = false,): void
    {
        $entity->setEntryDate(new DateTimeImmutable());
        $entity->setEmployee($employee);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProductionTime $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Employee $employee
     * @return Query Returns an array of ProductionTime objects
     */
    public function findByEmployee(Employee $employee): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.employee = :employee')
            ->setParameter('employee', $employee)
            ->orderBy('p.entryDate', 'DESC')
            ->getQuery()
        ;
    }

    public function findByProject(Project $project): Query
    {
        return $this->createQueryBuilder('p')
            ->where('p.project = :project')
            ->setParameter('project', $project)
            ->orderBy('p.entryDate', 'DESC')
            ->getQuery()
            ;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getNumberEmployee(Project $project): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT p.employee)')
            ->where('p.project = :project')
            ->setParameter('project', $project)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countProductionTime(): int
    {
        return $this->createQueryBuilder('p')
            ->select('SUM(p.productionTime)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getTenLastProductionTime(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.entryDate', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function bestEmployee(): array
    {
        return $this->createQueryBuilder('p')
            ->select('(SUM(p.productionTime)*e.dailyCost) as cost')
            ->addSelect('p as value')
            ->innerJoin('p.employee', 'e')
            ->groupBy('p.employee')
            ->orderBy('cost', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
