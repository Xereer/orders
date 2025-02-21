<?php

namespace App\Controller;

use App\Dto\UserDto;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    #[Route('/create', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): int
    {
        return $this->userService->createUser(json_decode($request->getContent(), true));
    }

    #[Route('/get', name: 'get_user', methods: ['GET'])]
    public function getUserData(): UserDto
    {
        return $this->userService->getUserData();
    }
}