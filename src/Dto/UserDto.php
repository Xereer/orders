<?php

namespace App\Dto;

use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

class UserDto
{
    #[Groups(['user'])]
    public int $id;

    #[Groups(['user'])]
    public string $name;

    #[Groups(['user'])]
    public string $login;
}