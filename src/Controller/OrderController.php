<?php

namespace App\Controller;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/order')]
class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly SerializerInterface|NormalizerInterface|DenormalizerInterface $serializer
    ) {}

    #[Route(path: '/create', name: 'create_order', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createOrder(Request $request): JsonResponse
    {
        $userId = $this->getUser()->getId();
        $orderId = $this->orderService->createOrder(json_decode($request->getContent(), true), $userId);
        $this->orderService->clearOrdersCache($userId);
        return new JsonResponse(data: $orderId, json: true);
    }

    #[Route(path: '/get/{userId}', name: 'get_orders', defaults: ['userId' => null], methods: ['GET'])]
    public function get(int $userId = null): JsonResponse
    {
        $user = $this->getUser();
        $orders = $this->serializer->serialize($this->orderService->getOrders($userId, $user), 'json');

        return new JsonResponse(data: $orders, json: true);
    }
}