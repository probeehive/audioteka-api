<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CartAddProductController;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => ['normalization_context' => ['groups' => 'cart:collection:get']],
    ],
)]
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     *
     * @Assert\Count(
     *     max = 3,
     *     maxMessage = "You cannot add more than {{ limit }} products"
     * )
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }

    /**
     *
     * @Groups("cart:collection:get")
     *
     * @return float
     */
    public function getCalculatedTotalAmount(): float
    {
        $totalAmount = 0;
        foreach ($this->getProducts() as $product) {
            $totalAmount += $product->getPrice();
        }
        return $totalAmount;
    }
}
