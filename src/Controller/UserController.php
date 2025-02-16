<?php

namespace App\Controller;

use App\Facade\UserFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserFacade $facade
    ) {}

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): int
    {
        return $this->facade->createUser(json_decode($request->getContent(), true));
    }
}