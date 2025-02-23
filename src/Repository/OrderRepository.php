<?php

namespace App\Repository;

use App\Enum\OrderStatusEnum;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\QueryException;

class OrderRepository extends EntityRepository
{
    /**
     * Получение заявок пользователя
     * @param int $userId
     * @param bool $isAdmin
     * @return array
     * @throws QueryException
     */
    public function getOrdersByUserId(int $userId, bool $isAdmin): array
    {
        $queryBuilder = $this->createQueryBuilder('o');
        $criteria = Criteria::create()->where(Criteria::expr()->eq('o.userId', $userId));

        if (!$isAdmin) {
            $criteria->andWhere(Criteria::expr()->neq('o.status', OrderStatusEnum::Deleted->value));
        }

        return $queryBuilder
            ->select('o.id, o.name, o.description')
            ->addCriteria($criteria)
            ->orderBy('o.id')
            ->getQuery()
            ->getResult();
    }
}