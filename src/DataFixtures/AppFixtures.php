<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use function file_get_contents;
use function json_decode;

class AppFixtures extends Fixture
{
    public function __construct() {}

    public function load(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents('./src/DataFixtures/data.json'), true);
        foreach ($data as $productData) {
            $product = new Product();
            $product->setName($productData['name']);
            $product->setShortDescription($productData['shortDescription']);
            if (isset($productData['fullDescription'])) {
                $product->setFullDescription($productData['fullDescription']);
            }
            $product->setPrice($productData['price']);
            $product->setPicture($productData['picture']);
            $manager->persist($product);
        }
        $manager->flush();
    }
}
