<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function save(Project $entity, bool $flush = false): void
    {
        $entity->setCreatedAt(new \DateTimeImmutable());
        $entity->setDeliveryDate(null);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Project $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function paginationQuery(): Query
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC')
            ->getQuery()
        ;
    }

    /**
     * @return ?Project
     * @throws NonUniqueResultException
     */
    public function findOneById(string $id): ?Project
    {
        return $this->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function deliverProject(Project $project): void
    {
        $project->setDeliveryDate(new \DateTimeImmutable());
        $this->getEntityManager()->flush();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countNotDeliveredProject(): int
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.deliveryDate IS NULL')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getDeliveredProject(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p as project')
            ->leftJoin('p.productionTimes', 'm')
            ->leftJoin('m.employee', 'e')
            ->addSelect('e, m, SUM(m.productionTime*e.dailyCost) as total')
            ->groupBy('p.id')
            ->where('p.deliveryDate IS NOT NULL')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getFiveLastProjects(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p as project')
            ->leftJoin('p.productionTimes', 'm')
            ->leftJoin('m.employee', 'e')
            ->addSelect('e,m ,SUM(m.productionTime*e.dailyCost) as total')
            ->groupBy('p.id')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return Project[] Returns an array of Project objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
