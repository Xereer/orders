<?php

namespace App\Tests\Unit\Service;

use App\Dto\UserDto;
use App\Entity\UserEntity;
use App\Enum\PrivilegeThesaurus;
use App\Enum\UserStatusEnum;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class UserServiceTest extends TestCase
{
    private $entityManager;
    private $passwordHasher;
    private $jwtTokenManager;
    private $userService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->jwtTokenManager = $this->createMock(JWTTokenManagerInterface::class);

        $this->userService = new UserService(
            $this->entityManager,
            $this->passwordHasher,
            $this->jwtTokenManager
        );
    }

    public function testCreateUser(): void
    {
        $userData = [
            'name' => 'John Doe',
            'login' => 'johndoe',
            'password' => 'password123',
            'email' => 'john@example.com'
        ];

        $this->passwordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->willReturn('hashed_password');

        $this->entityManager
            ->expects($this->once())
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->jwtTokenManager
            ->expects($this->once())
            ->method('create')
            ->willReturn('jwt_token');

        $token = $this->userService->createUser($userData);

        $this->assertEquals('jwt_token', $token);
    }

    public function testGetUserData(): void
    {
        $user = new UserEntity();
        $user->setName('John Doe')
            ->setLogin('johndoe')
            ->setEmail('john@example.com')
            ->setStatus(UserStatusEnum::Active->value);

        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($user, 1);

        $expectedDto = new UserDto();
        $expectedDto->id = 1;
        $expectedDto->name = 'John Doe';
        $expectedDto->login = 'johndoe';

        $userMock = $this->createMock(UserEntity::class);
        $userMock->expects($this->once())
            ->method('entityToDto')
            ->with(UserDto::class)
            ->willReturn($expectedDto);

        $result = $this->userService->getUserData($userMock);

        $this->assertEquals($expectedDto, $result);
    }

    public function testDeleteUser(): void
    {
        $userId = 1;
        $user = new UserEntity();
        $user->setStatus(UserStatusEnum::Active->value);

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(UserEntity::class, $userId)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('beginTransaction');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->entityManager
            ->expects($this->once())
            ->method('commit');

        $this->userService->deleteUser($userId);

        $this->assertEquals(UserStatusEnum::Deleted->value, $user->getStatus());
    }

    public function testGrantPrivilege(): void
    {
        $userId = 1;
        $privilege = PrivilegeThesaurus::ROLE_ADMIN->value;
        $user = new UserEntity();
        $user->setRoles(['ROLE_USER']);

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(UserEntity::class, $userId)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->userService->grantPrivilege($userId, $privilege);

        $expectedRoles = ['ROLE_USER', 'ROLE_ADMIN'];
        $this->assertEquals($expectedRoles, $user->getRoles());
    }

    public function testRevokePrivilege(): void
    {
        $userId = 1;
        $privilege = PrivilegeThesaurus::ROLE_ADMIN->value;
        $user = new UserEntity();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);

        $this->entityManager
            ->expects($this->once())
            ->method('find')
            ->with(UserEntity::class, $userId)
            ->willReturn($user);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->userService->revokePrivilege($userId, $privilege);

        $expectedRoles = ['ROLE_USER'];
        $this->assertEquals($expectedRoles, $user->getRoles());
    }
}