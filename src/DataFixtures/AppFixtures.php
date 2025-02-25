<?php

namespace App\DataFixtures;

use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\UserRepository;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function load(ObjectManager $manager): void
    {
        UserFactory::new()->createMany(30);
        ProductFactory::new()->createMany(100);

        $manager->flush();
    }
}
