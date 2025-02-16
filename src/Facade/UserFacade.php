<?php

namespace App\Facade;

use App\Entity\UserEntity;
use App\Enum\UserStatusEnum;
use App\Service\UserService;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;

class UserFacade
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly UserService $userService
    ) {}

    public function createUser(array $userData): int
    {
        $user = (new UserEntity())
            ->setName($userData['name'])
            ->setLogin($userData['login'])
            ->setIsAdmin(false)
            ->setRegistrationDate(new DateTime())
            ->setStatus(UserStatusEnum::Active->value);

        $this->managerRegistry->getManager()->persist($user);
        $this->managerRegistry->getManager()->flush();

        return $user->getId();
    }
}