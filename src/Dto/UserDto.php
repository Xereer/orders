<?php

namespace App\Dto;

use DateTime;
use Symfony\Component\Serializer\Annotation\Groups;

class UserDto
{
    public int $id;

    public string $name;

    public DateTime $registrationDate;
}