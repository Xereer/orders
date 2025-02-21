<?php

namespace App\Entity;

use ReflectionClass;

class BaseEntity
{
    public function entityToDto(string $className): object
    {
        $dto = new $className;

        $dtoReflection = new ReflectionClass($className);
        $entity = new ReflectionClass($this);

        foreach ($dtoReflection->getProperties() as $property) {
            $getMethod = 'get' . ucfirst($property->getName());

            if ($entity->hasMethod($getMethod)) {
                $property->setValue($dto, $this->$getMethod());
            }
        }

        return $dto;
    }
}