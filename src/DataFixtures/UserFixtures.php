<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    /**
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'email' => 'joe@doe.com',
                'password' => 'Pass123',
            ],
            [
                'email' => 'user@user.com',
                'password' => 'Pass123',
            ],
        ];

        foreach ($users as $_user) {
            $user = new User();
            $user->setEmail($_user['email']);
            $user->setRoles([
                'ROLE_USER',
            ]);

            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $_user['password']
            );

            $user->setPassword($hashedPassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
