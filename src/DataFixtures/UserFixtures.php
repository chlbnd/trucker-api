<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setEmail('admin@truckpad.com')
            ->setPassword('$argon2i$v=19$m=65536,t=4,p=1$emw3djJLRllnYjBtYkVRaw$I1M9Ci8O1gsMkabskhWU9OpdHfMSzUywZGi9Tp5lvyQ');

        $manager->persist($user);
        $manager->flush();
    }
}
