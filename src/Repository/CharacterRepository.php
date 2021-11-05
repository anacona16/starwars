<?php

namespace App\Repository;

use App\Entity\Character;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Character|null find($id, $lockMode = null, $lockVersion = null)
 * @method Character|null findOneBy(array $criteria, array $orderBy = null)
 * @method Character[]    findAll()
 * @method Character[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Character::class);
    }

    /**
     * Returns Characters from DB.
     *
     * @param $limit
     * @param $page
     * @return int|mixed|string
     */
    public function findAllPaginated($limit, $page)
    {
        return $this
            ->createQueryBuilder('character')
            ->where('character.group_page = :group_page')
            ->setParameters([
                'group_page' => $page,
            ])
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
