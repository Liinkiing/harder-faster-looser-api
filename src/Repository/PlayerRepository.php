<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Player|null find($id, $lockMode = null, $lockVersion = null)
 * @method Player|null findOneBy(array $criteria, array $orderBy = null)
 * @method Player[]    findAll()
 * @method Player[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * @param int $first
     * @return Player[]
     */
    public function findFirst(int $first): array
    {
        $rsm = new ResultSetMappingBuilder($this->getEntityManager());
        $rsm->addRootEntityFromClassMetadata(Player::class, 'p');
        $rsm->addScalarResult('rank', 'rank', 'integer');
        $query = $this->getEntityManager()->createNativeQuery(
            'SELECT id, score, username, created_at, @curRank := @curRank + 1 AS rank FROM player p, (SELECT @curRank := 0) q ORDER BY score DESC, created_at ASC LIMIT ' . $first,
            $rsm
        );
        $results = array_map(function ($resultSet) {
            /** @var Player $player */
            $player = $resultSet[0];
            $player->setRank($resultSet['rank']);

            return $player;
        }, $query->getResult());

        return $results;
    }

    /**
     * @param int $score
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findRankForScore(int $score): int
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->select(
                $qb->expr()->count('p.id')
            )
            ->andWhere(
                $qb->expr()->gt('p.score', ':score')
            )
            ->orWhere(
                $qb->expr()->andX($qb->expr()->eq('p.score', ':score'), $qb->expr()->lt('p.createdAt', ':created'))
            )
            ->setParameter('score', $score)
            ->setParameter('created', new \DateTime());

        return (int)$qb->getQuery()->getSingleScalarResult() + 1;
    }
}
