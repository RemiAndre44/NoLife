<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StarRepository")
 */
class Star
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="smallint")
     */
    private $rate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="stars")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="stars")
     */
    private $movie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bds", inversedBy="stars")
     */
    private $bds;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

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

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getBds(): ?Bds
    {
        return $this->bds;
    }

    public function setBds(?Bds $bds): self
    {
        $this->bds = $bds;

        return $this;
    }
}
