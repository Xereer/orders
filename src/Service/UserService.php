<?php

namespace App\Service;

use App\Dto\UserDto;
use App\Entity\UserEntity;
use App\Enum\UserStatusEnum;
use DateTime;

class UserService extends BaseService
{
    public function createUser(array $userData): int
    {
        $user = (new UserEntity())
            ->setName($userData['name'])
            ->setLogin($userData['login'])
            ->setIsAdmin(false)
            ->setRegistrationDate(new DateTime())
            ->setStatus(UserStatusEnum::Active->value);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
    }

    public function getUserData(): UserDto
    {
        $user = $this->entityManager->find(UserEntity::class, 16)->entityToDto(UserDto::class);
        var_dump($this->serializer->serialize($user, 'json', ['groups' => 'user:read']));die;
        return 1;
    }

}