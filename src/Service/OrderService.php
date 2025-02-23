<?php

namespace App\Service;

use App\Entity\OrderEntity;
use App\Entity\UserEntity;
use App\Enum\OrderStatusEnum;
use App\Enum\PrivilegeEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CacheInterface         $cache
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
        $isAdmin = true;
        if (!in_array(PrivilegeEnum::ROLE_ADMIN->name, $user->getRoles())) {
            $userId = $user->getId();
            $isAdmin = false;
        } elseif (!isset($userId)) {
            $userId = $user->getId();
        } else {
            throw new Exception('Отсутствует id пользователя');
        }
        $cacheKey = "user_orders_{$userId}";

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($userId, $isAdmin) {
            $item->expiresAfter(3600);
            return $this->entityManager->getRepository(OrderEntity::class)->getOrdersByUserId($userId, $isAdmin);
        });
    }

    public function deleteOrder(UserInterface $user, int $orderId): void
    {
        $params = ['id' => $orderId];

        if (!in_array(PrivilegeEnum::ROLE_ADMIN->name, $user->getRoles())) {
            $params['userId'] = $user->getId();
        }

        $order = $this->entityManager->getRepository(OrderEntity::class)->findOneBy($params);
        if (is_null($order)) {
            throw new Exception('Заявка не найдена');
        }

        $order->setStatus(OrderStatusEnum::Deleted->value);

        $this->entityManager->flush();
    }

    public function clearOrdersCache(int $userId): void
    {
        $this->cache->delete("user_orders_{$userId}");
    }
}