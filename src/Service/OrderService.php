<?php

namespace App\Service;

use App\Entity\OrderEntity;
use App\Entity\UserEntity;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {}

    public function createOrder(): int
    {
        $order = (new OrderEntity())
            ->setName('Заказ')
            ->setCreateDate(new DateTime())
            ->setUser($this->entityManager->getReference(UserEntity::class, 16));

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order->getId();
    }
}