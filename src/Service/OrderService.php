<?php

namespace App\Service;

use App\Entity\OrderEntity;
use App\Entity\UserEntity;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class OrderService
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
    ) {}

    public function createOrder(): int
    {
        $order = (new OrderEntity())
            ->setName('Заказ')
            ->setCreateDate(new DateTime())
            ->setUser($this->managerRegistry->getManager()->find(UserEntity::class, 16));

        $this->managerRegistry->getManager()->persist($order);
        $this->managerRegistry->getManager()->flush();

        return $order->getId();
    }
}