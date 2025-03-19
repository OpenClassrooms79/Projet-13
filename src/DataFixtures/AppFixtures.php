<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Entity\Product;
use App\Factory\OrderDetailFactory;
use App\Factory\OrderFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\Repository\OrderRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

use function file_get_contents;
use function json_decode;
use function random_int;

class AppFixtures extends Fixture
{
    public function __construct(private OrderRepository $orderRepository) {}

    public function load(ObjectManager $manager): void
    {
        $this->loadProducts($manager);
        $this->loadUsers($manager);
        $this->loadOrders($manager);
        $this->loadOrderDetails($manager);
        $this->updateOrdersTotal($manager);
        $manager->flush();
    }

    public function loadProducts(ObjectManager $manager): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data.json'), true, 512, JSON_THROW_ON_ERROR);
        $faker = Faker\Factory::create();

        foreach ($data as $productData) {
            ProductFactory::createOne([
                'name' => $productData['name'],
                'shortDescription' => $productData['shortDescription'],
                'fullDescription' => $productData['fullDescription'] ?? $faker->text(random_int(500, 1000)),
                'price' => $productData['price'],
                'picture' => $productData['picture'],
            ]);
        }

        // ajout de produits aléatoires
        ProductFactory::createMany(20);
    }

    public function loadUsers(ObjectManager $manager): void
    {
        UserFactory::createMany(20);
        //UserFactory::createMany(2);
    }

    public function loadOrders(ObjectManager $manager): void
    {
        OrderFactory::createMany(100);
        //OrderFactory::createMany(3);
    }

    public function loadOrderDetails(ObjectManager $manager): void
    {
        OrderDetailFactory::new()->createMany(1000);
        //OrderDetailFactory::createMany(10);
    }

    private function updateOrdersTotal(ObjectManager $manager): void
    {
        /** @var Order[] $orders */
        $orders = $this->orderRepository->findAll();

        foreach ($orders as $order) {
            $order->recalculateTotal();
            $manager->persist($order);
        }
    }
}
