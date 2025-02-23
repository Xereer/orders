<?php

namespace App\Tests\Unit\Service;

use App\Entity\OrderEntity;
use App\Entity\UserEntity;
use App\Enum\OrderStatusEnum;
use App\Enum\PrivilegeThesaurus;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderServiceTest extends TestCase
{
    private $entityManager;
    private $cache;
    private $orderService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);

        $this->orderService = new OrderService(
            $this->entityManager,
            $this->cache
        );
    }

    public function testCreateOrder(): void
    {
        $orderData = [
            'name' => 'Test Order',
            'descriprion' => 'Test Description',
        ];
        $userId = 1;

        $user = new UserEntity();

        $order = new OrderEntity();

        $reflection = new \ReflectionClass($order);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($order, 123);

        $this->entityManager
            ->expects($this->once())
            ->method('getReference')
            ->with(UserEntity::class, $userId)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(OrderEntity::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $orderId = $this->orderService->createOrder($orderData, $userId);

        $this->assertEquals(123, $orderId);
    }

    public function testGetOrdersForAdminWithUserId(): void
    {
        $userId = 1;
        $user = $this->createMock(UserEntity::class);
        $user->method('getRoles')->willReturn([PrivilegeThesaurus::ROLE_ADMIN->name]);
        $user->method('getId')->willReturn($userId);

        $expectedOrders = [
            new OrderEntity(),
            new OrderEntity(),
        ];

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with("user_orders_{$userId}", $this->isType('callable'))
            ->willReturnCallback(function ($key, $callback) use ($expectedOrders) {
                return $callback($this->createMock(UserEntity::class));
            });

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('getOrdersByUserId')
            ->with($userId, true)
            ->willReturn($expectedOrders);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(OrderEntity::class)
            ->willReturn($orderRepository);

        $orders = $this->orderService->getOrders($userId, $user);

        $this->assertEquals($expectedOrders, $orders);
    }

    public function testGetOrdersForAdminWithoutUserId(): void
    {
        $userId = 1;
        $user = $this->createMock(UserEntity::class);
        $user->method('getRoles')->willReturn([PrivilegeThesaurus::ROLE_ADMIN->name]);
        $user->method('getId')->willReturn($userId);

        $expectedOrders = [
            new OrderEntity(),
        ];

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with("user_orders_{$userId}", $this->isType('callable'))
            ->willReturnCallback(function ($key, $callback) use ($expectedOrders) {
                return $callback($this->createMock(UserInterface::class));
            });

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('getOrdersByUserId')
            ->with($userId, true)
            ->willReturn($expectedOrders);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(OrderEntity::class)
            ->willReturn($orderRepository);

        $orders = $this->orderService->getOrders(null, $user);

        $this->assertEquals($expectedOrders, $orders);
    }

    public function testGetOrdersForNonAdmin(): void
    {
        $userId = 1;
        $user = $this->createMock(UserEntity::class);
        $user->method('getRoles')->willReturn(['ROLE_USER']);
        $user->method('getId')->willReturn($userId);

        $expectedOrders = [
            new OrderEntity(),
        ];

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with("user_orders_{$userId}", $this->isType('callable'))
            ->willReturnCallback(function ($key, $callback) use ($expectedOrders) {
                return $callback($this->createMock(UserInterface::class));
            });

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository
            ->expects($this->once())
            ->method('getOrdersByUserId')
            ->with($userId, false)
            ->willReturn($expectedOrders);

        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(OrderEntity::class)
            ->willReturn($orderRepository);

        $orders = $this->orderService->getOrders(null, $user);

        $this->assertEquals($expectedOrders, $orders);
    }

    public function testGetOrdersThrowsExceptionWhenUserIdIsMissing(): void
    {
        $user = $this->createMock(UserEntity::class);
        $user->method('getRoles')->willReturn(['ROLE_USER']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Отсутствует id пользователя');

        $this->orderService->getOrders(null, $user);
    }

    public function testClearOrdersCache(): void
    {
        $userId = 1;

        $this->cache
            ->expects($this->once())
            ->method('delete')
            ->with("user_orders_{$userId}");

        $this->orderService->clearOrdersCache($userId);
    }
}