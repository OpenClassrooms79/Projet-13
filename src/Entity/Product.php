<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Symfony\Action\NotFoundAction;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use function floor;
use function number_format;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(operations: [
    new Get(
        controller: NotFoundAction::class,
    ),
    new GetCollection(),
])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, options: ['default' => ''])]
    private ?string $shortDescription = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $fullDescription = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?float $price = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    public function __construct()
    {
        $this->fullDescription = '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getFullDescription(): string
    {
        return $this->fullDescription;
    }

    public function setFullDescription(string $fullDescription): static
    {
        $this->fullDescription = $fullDescription;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function __toString(): string
    {
        return print_r([
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'shortDescription' => $this->shortDescription,
            'fullDescription' => $this->fullDescription,
            'picture' => $this->picture,
        ], true);
    }
}
