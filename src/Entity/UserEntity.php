<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\{Entity, GeneratedValue, Id, Column, OneToMany, SequenceGenerator, Table};
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[Entity]
#[Table(name: 'USERS')]
class UserEntity extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Id]
    #[GeneratedValue(strategy: 'SEQUENCE')]
    #[SequenceGenerator(sequenceName: 'user_sequence', allocationSize: 1, initialValue: 1)]
    #[Column(name: 'ID', type: Types::INTEGER, nullable: false)]
    private int $id;

    #[Column(name: 'NAME', type: Types::STRING, nullable: false)]
    private string $name;

    #[Column(name: 'LOGIN', type: Types::STRING, nullable: false)]
    private string $login;

    #[Column(name: 'EMAIL', type: Types::STRING, nullable: false)]
    private string $email;

    #[Column(name: 'PASSWORD', type: Types::STRING, nullable: true)]
    private string $password;

    #[Column(name: 'REGISTRATION_DATE', type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTime $registrationDate;

    #[Column(name: 'ROLES', type: Types::JSON, nullable: false)]
    private array $roles;

    #[Column(name: 'END_DATE', type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $endDate;

    #[Column(name: 'STATUS', type: Types::INTEGER, nullable: false)]
    private int $status;

    #[OneToMany(targetEntity: OrderEntity::class, mappedBy: 'user')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return UserEntity
     */
    public function setName(string $name): UserEntity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return UserEntity
     */
    public function setLogin(string $login): UserEntity
    {
        $this->login = $login;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRegistrationDate(): DateTime
    {
        return $this->registrationDate;
    }

    /**
     * @param DateTime $registrationDate
     * @return UserEntity
     */
    public function setRegistrationDate(DateTime $registrationDate): UserEntity
    {
        $this->registrationDate = $registrationDate;
        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $role
     * @return UserEntity
     */
    public function setRoles(array $role): UserEntity
    {
        $this->roles = $role;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    /**
     * @param DateTime|null $endDate
     * @return UserEntity
     */
    public function setEndDate(?DateTime $endDate): UserEntity
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return UserEntity
     */
    public function setStatus(int $status): UserEntity
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return UserEntity
     */
    public function setEmail(string $email): UserEntity
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return UserEntity
     */
    public function setPassword(string $password): UserEntity
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->id;
    }
}