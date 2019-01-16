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
            'SELECT id, score, username, @curRank := @curRank + 1 AS rank FROM player p, (SELECT @curRank := 0) q ORDER BY score DESC LIMIT ' . $first,
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

}
