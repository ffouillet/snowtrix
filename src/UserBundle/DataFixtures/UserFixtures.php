<?php


namespace UserBundle\DataFixtures;

use UserBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Create Demo User
        $user = new User();
        $user->setUsername('Demo');
        $user->setEmail('replace-with-your-email@email.com');
        $user->setPlainPassword('ocdemo');

        $manager->persist($user);
        $manager->flush();
    }
}