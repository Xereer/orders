<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping\{Column, Entity, GeneratedValue, Id, JoinColumn, ManyToOne, SequenceGenerator, Table};
use DateTime;
use Doctrine\DBAL\Types\Types;

#[Entity(repositoryClass: OrderRepository::class)]
#[Table(name: 'ORDERS')]
class OrderEntity extends BaseEntity
{
    #[Id]
    #[GeneratedValue(strategy: 'SEQUENCE')]
    #[SequenceGenerator(sequenceName: 'order_sequence', allocationSize: 1, initialValue: 1)]
    #[Column(name: 'ID', type: Types::INTEGER, nullable: false)]
    private int $id;

    #[Column(name: 'NAME', type: Types::STRING, nullable: false)]
    private string $name;

    #[Column(name: 'CREATE_DATE', type: Types::DATE_MUTABLE, nullable: false)]
    private DateTime $createDate;

    #[Column(name: 'USER_ID', type: Types::INTEGER, nullable: false)]
    private int $userId;

    #[Column(name: 'DESCRIPTION', type: Types::STRING, nullable: true)]
    private string $description;

    #[Column(name: 'VALID_TO', type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $validTo;

    #[Column(name: 'STATUS', type: Types::INTEGER, nullable: false)]
    private int $status;

    #[ManyToOne(targetEntity: UserEntity::class, inversedBy: 'orders')]
    #[JoinColumn(name: 'USER_ID', referencedColumnName: 'ID', nullable: false)]
    private UserEntity $user;

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
     * @return OrderEntity
     */
    public function setName(string $name): OrderEntity
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreateDate(): DateTime
    {
        return $this->createDate;
    }

    /**
     * @param DateTime $createDate
     * @return OrderEntity
     */
    public function setCreateDate(DateTime $createDate): OrderEntity
    {
        $this->createDate = $createDate;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return OrderEntity
     */
    public function setUserId(int $userId): OrderEntity
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return OrderEntity
     */
    public function setDescription(string $description): OrderEntity
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getValidTo(): ?DateTime
    {
        return $this->validTo;
    }

    /**
     * @param DateTime|null $validTo
     * @return OrderEntity
     */
    public function setValidTo(?DateTime $validTo): OrderEntity
    {
        $this->validTo = $validTo;
        return $this;
    }

    /**
     * @return UserEntity
     */
    public function getUser(): UserEntity
    {
        return $this->user;
    }

    /**
     * @param UserEntity $user
     * @return OrderEntity
     */
    public function setUser(UserEntity $user): OrderEntity
    {
        $this->user = $user;
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
     * @return OrderEntity
     */
    public function setStatus(int $status): OrderEntity
    {
        $this->status = $status;
        return $this;
    }
}