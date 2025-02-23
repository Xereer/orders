<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user')]
class UserController extends AbstractController {
    public function __construct(
        private readonly UserService $userService,
        private readonly SerializerInterface|NormalizerInterface|DenormalizerInterface $serializer,
    ) {}

    #[Route('/create', name: 'create_user', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        return new JsonResponse(data: $this->userService->createUser(json_decode($request->getContent(), true)), json: true);
    }

    #[Route('/login', name: 'user_login', methods: ['GET'])]
    public function login(): void {}

    #[Route('/get', name: 'get_user', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function get(): JsonResponse
    {
        $user = $this->getUser();
        $userData = $this->serializer->serialize($this->userService->getUserData($user), 'json', ['groups' => ['user']]);
        return new JsonResponse(data: $userData, json: true);
    }

    #[Route('/delete/{id}', name: 'delete_user', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(int $id): JsonResponse
    {
        $this->userService->deleteUser($id);
        return new JsonResponse('Успех');
    }

    #[Route('/grant', name: 'grant_privilege', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function grantPrivilege(Request $request): JsonResponse
    {
        $request = json_decode($request->getContent(), true);
        $this->userService->grantPrivilege($request['userId'], $request['role']);
        return new JsonResponse('Успех');
    }

    #[Route('/revoke', name: 'revoke_privilege', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function revokePrivilege(Request $request): JsonResponse
    {
        $request = json_decode($request->getContent(), true);
        $this->userService->revokePrivilege($request['userId'], $request['role']);
        return new JsonResponse('Успех');
    }
}