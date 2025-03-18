<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'CMD-' . strtoupper(bin2hex(random_bytes(5)));
        } while ($this->findOneBy(['num' => $orderNumber]));

        return $orderNumber;
    }

    public function findRandom(): ?Order
    {
        return $this
            ->createQueryBuilder('o')
            ->orderBy('RAND()')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
