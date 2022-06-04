<?php

namespace App\Repository;

use App\DataFixtures\PersonneFixture;
use App\Entity\Personne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Personne>
 *
 * @method Personne|null find($id, $lockMode = null, $lockVersion = null)
 * @method Personne|null findOneBy(array $criteria, array $orderBy = null)
 * @method Personne[]    findAll()
 * @method Personne[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personne::class);
    }

    public function add(Personne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Personne $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPersonByAgeInterval($ageMin, $ageMax): array
    {
        $qb = $this->createQueryBuilder('p');
        $this->addIntervalAge($qb, $ageMin, $ageMax);
        return $qb->getQuery()
            ->getResult();

    }

    public function statsPersonByAgeInterval($ageMin,  $ageMax): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('avg(p.age) as moyenne, count(p.id) as nbPersonnes');
        $this->addIntervalAge($qb, $ageMin, $ageMax);
        return $qb->getQuery()->getScalarResult();

    }

    private function addIntervalAge(QueryBuilder $qb, $ageMin, $ageMax): void
    {
        $qb->Where('p.age >= :ageMin and p.age <= :ageMax')
            ->setParameters(['ageMax' => $ageMax, 'ageMin' => $ageMin]);
    }


}
