<?php

namespace App\Controller;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly OrderService $orderService
    ) {}

    #[Route('/orders', name: 'create_order', methods: ['POST'])]
    public function createOrder(): int
    {
        return $this->orderService->createOrder();
    }
}