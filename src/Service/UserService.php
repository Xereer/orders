<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
class UserService
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    public function createUser(): int
    {
        return 1;
    }
}