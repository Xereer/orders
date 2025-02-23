<?php

namespace App\Service;

use App\Dto\UserDto;
use App\Entity\OrderEntity;
use App\Entity\UserEntity;
use App\Enum\OrderStatusEnum;
use App\Enum\PrivilegeThesaurus;
use App\Enum\UserStatusEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService extends BaseService
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly JWTTokenManagerInterface $JWTTokenManager
    ) {
        parent::__construct($entityManager);
    }

    public function createUser(array $userData): string
    {
        $user = (new UserEntity())
            ->setName($userData['name'])
            ->setLogin($userData['login'])
            ->setEmail('gleb.romanov2002@mail.ru')
            ->setRegistrationDate(new DateTime())
            ->setRoles(['ROLE_USER'])
            ->setStatus(UserStatusEnum::Active->value);

        $user->setPassword($this->passwordHasher->hashPassword($user, $userData['password']));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->JWTTokenManager->create($user);
    }

    public function getUserData(UserInterface $user): UserDto
    {
        return $user->entityToDto(UserDto::class);
    }

    /**
     * @throws ORMException
     */
    public function deleteUser(int $id): void
    {
        $this->entityManager->beginTransaction();
        try {
            $user = $this->entityManager->find(UserEntity::class, $id);
            $user->setStatus(UserStatusEnum::Deleted->value);

            $orders = $user->getOrders();
            $orders->map(fn(OrderEntity $order): OrderEntity => $order->setStatus(OrderStatusEnum::Deleted->value));

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Exception $exception) {
            $this->entityManager->rollback();
            throw new Exception($exception->getMessage());
        }
    }

    public function grantPrivilege(int $userId, int $privilege): void
    {
        $privilegeName = [PrivilegeThesaurus::from($privilege)->name];
        $user = $this->entityManager->find(UserEntity::class, $userId);

        $newPrivileges = array_merge($user->getRoles(), $privilegeName);

        $user->setRoles(array_unique($newPrivileges));
        $this->entityManager->flush();
    }

    public function revokePrivilege(int $userId, int $privilege): void
    {
        $privilegeName = [PrivilegeThesaurus::from($privilege)->name];
        $user = $this->entityManager->find(UserEntity::class, $userId);

        $user->setRoles(array_diff($user->getRoles(), $privilegeName));
        $this->entityManager->flush();
    }
}