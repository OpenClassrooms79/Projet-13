<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

use function file_get_contents;
use function json_decode;
use function random_int;

class AppFixtures extends Fixture
{
    public function __construct() {}

    public function load(ObjectManager $manager): void
    {
        $this->loadProducts($manager);
        $this->loadUsers($manager);
        $manager->flush();
    }

    public function loadProducts(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents('./src/DataFixtures/data.json'), true, 512, JSON_THROW_ON_ERROR);
        $faker = Faker\Factory::create();

        foreach ($data as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setShortDescription($productData['shortDescription']);
            if (isset($productData['fullDescription'])) {
                $product->setFullDescription($productData['fullDescription']);
            } else {
                $product->setFullDescription($faker->text(random_int(500, 1000)));
            }
            $product->setPrice($productData['price']);
            $product->setPicture($productData['picture']);
            $manager->persist($product);
        }
    }

    public function loadUsers(ObjectManager $manager): void
    {
        UserFactory::createMany(20);
    }
}
