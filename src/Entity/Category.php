<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="category")
     */
    private $list_articles;

    public function __construct()
    {
        $this->list_articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Article[]
     */
    public function getListArticles(): Collection
    {
        return $this->list_articles;
    }

    public function addListArticle(Article $listArticle): self
    {
        if (!$this->list_articles->contains($listArticle)) {
            $this->list_articles[] = $listArticle;
            $listArticle->setCategory($this);
        }

        return $this;
    }

    public function removeListArticle(Article $listArticle): self
    {
        if ($this->list_articles->contains($listArticle)) {
            $this->list_articles->removeElement($listArticle);
            // set the owning side to null (unless already changed)
            if ($listArticle->getCategory() === $this) {
                $listArticle->setCategory(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->title;
    }
}
