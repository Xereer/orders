<?php

namespace App\DataFixtures;

use App\Entity\OrderEntity;
use App\Entity\UserEntity;
use App\Enum\UserStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();

        $privileges = ['ROLE_USER', 'ROLE_ADMIN'];
        $randomKey = array_rand($privileges);


        for ($i = 0; $i < 5; $i++) {

            $user = new UserEntity();
            $user->setName($faker->name)
                ->setLogin($faker->userName)
                ->setEmail($faker->email)
                ->setRoles([$privileges[$randomKey]])
                ->setPassword($this->passwordHasher->hashPassword($user, '1234'))
                ->setRegistrationDate($faker->dateTimeThisDecade)
                ->setStatus(UserStatusEnum::Active->value);

            $manager->persist($user);

            for ($j = 0; $j < 3; $j++) {
                $order = new OrderEntity();
                $order->setName($faker->word)
                    ->setCreateDate($faker->dateTimeThisYear)
                    ->setUser($user)
                    ->setDescription($faker->sentence)
                    ->setStatus(rand(1, 5));
                $manager->persist($order);
            }
        }

        $manager->flush();
    }
}
