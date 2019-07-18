<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuoteRepository")
 */
class Quote
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="quotes")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuoteLike", mappedBy="quote")
     */
    private $quoteLikes;

    public function __construct()
    {
        $this->quoteLikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return Collection|QuoteLike[]
     */
    public function getQuoteLikes(): Collection
    {
        return $this->quoteLikes;
    }

    public function addQuoteLike(QuoteLike $quoteLike): self
    {
        if (!$this->quoteLikes->contains($quoteLike)) {
            $this->quoteLikes[] = $quoteLike;
            $quoteLike->setQuote($this);
        }

        return $this;
    }

    public function removeQuoteLike(QuoteLike $quoteLike): self
    {
        if ($this->quoteLikes->contains($quoteLike)) {
            $this->quoteLikes->removeElement($quoteLike);
            // set the owning side to null (unless already changed)
            if ($quoteLike->getQuote() === $this) {
                $quoteLike->setQuote(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(User $user): bool
    {
        foreach ($this->quoteLikes as $like) {
            if($like->getUser() === $user) return true;
        }

        return false;
    }
}
