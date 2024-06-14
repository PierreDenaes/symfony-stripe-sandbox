<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'baskets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $idUser = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isPaid = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $stripeSessionId = null;

    /**
     * @var Collection<int, BasketProduct>
     */
    #[ORM\OneToMany(mappedBy: 'basket', targetEntity: BasketProduct::class, cascade: ['persist', 'remove'])]
    private Collection $basketProducts;

    public function __construct()
    {
        $this->basketProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIsPaid(): bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): static
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): static
    {
        $this->stripeSessionId = $stripeSessionId;

        return $this;
    }

    /**
     * @return Collection<int, BasketProduct>
     */
    public function getBasketProducts(): Collection
    {
        return $this->basketProducts;
    }

    public function addBasketProduct(BasketProduct $basketProduct): static
    {
        if (!$this->basketProducts->contains($basketProduct)) {
            $this->basketProducts->add($basketProduct);
            $basketProduct->setBasket($this);
        }

        return $this;
    }

    public function removeBasketProduct(BasketProduct $basketProduct): static
    {
        if ($this->basketProducts->removeElement($basketProduct)) {
            if ($basketProduct->getBasket() === $this) {
                $basketProduct->setBasket(null);
            }
        }

        return $this;
    }
    public function getTotalAmount(): float
    {
        $total = 0;
        foreach ($this->basketProducts as $basketProduct) {
            $total += $basketProduct->getProduct()->getPrix() * $basketProduct->getQuantity();
        }
        return $total;
    }
}