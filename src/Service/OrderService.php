<?php

namespace App\Service;

use App\Entity\OrderEntity;
use App\Entity\UserEntity;
use App\Enum\OrderStatusEnum;
use App\Enum\PrivilegeThesaurus;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CacheInterface $cache
    ) {}

    public function createOrder(array $orderData, int $userId): int
    {
        $order = (new OrderEntity())
            ->setName($orderData['name'])
            ->setCreateDate(new DateTime())
            ->setDescription($orderData['descriprion'])
            ->setUser($this->entityManager->getReference(UserEntity::class, $userId))
            ->setStatus(OrderStatusEnum::Created->value);

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order->getId();
    }

    public function getOrders(?int $userId, UserInterface $user): array
    {
        $isAdmin = false;
        if (!in_array(PrivilegeThesaurus::ROLE_ADMIN->name, $user->getRoles())) {
            $userId = $user->getId();
            $isAdmin = true;
        }
        $cacheKey = "user_orders_{$userId}";

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($userId, $isAdmin) {
            $item->expiresAfter(3600);
            return $this->entityManager->getRepository(OrderEntity::class)->getOrdersByUserId($userId, $isAdmin);
        });
    }

    public function clearOrdersCache(int $userId): void
    {
        $this->cache->delete("user_orders_{$userId}");
    }
}