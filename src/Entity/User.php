<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable
 * @UniqueEntity(
 *     fields={"email"},
 *     message= "L'email que vous avez indiqué est déja utilisé",
 *     )
 */
class User implements UserInterface
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
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="6", minMessage="C'est bien trop court...même si c'est pas la taille qui compte bien sûr ;)")"
     * @Assert\EqualTo(propertyPath="confirm_password", message="Les deux champs doivent être identiques")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="upload", fileNameProperty="image")
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les deux champs doivent être identiques")
     */
    public $confirm_password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Article", mappedBy="author")
     */
    private $list_articles;

    /**
     * @ORM\Column(type="smallint")
     */
    private $profil;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $request;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PostLike", mappedBy="user")
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quote", mappedBy="user")
     */
    private $quotes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuoteLike", mappedBy="user")
     */
    private $quoteLikes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommentLike", mappedBy="user")
     */
    private $commentLikes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Star", mappedBy="user")
     */
    private $stars;


    public function __construct()
    {
        $this->list_articles = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->quotes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->quoteLikes = new ArrayCollection();
        $this->commentLikes = new ArrayCollection();
        $this->stars = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function getUsername(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return Collection|article[]
     */
    public function getListArticles(): Collection
    {
        return $this->list_articles;
    }

    public function addListArticle(article $listArticle): self
    {
        if (!$this->list_articles->contains($listArticle)) {
            $this->list_articles[] = $listArticle;
            $listArticle->setUser($this);
        }

        return $this;
    }

    public function removeListArticle(article $listArticle): self
    {
        if ($this->list_articles->contains($listArticle)) {
            $this->list_articles->removeElement($listArticle);
            // set the owning side to null (unless already changed)
            if ($listArticle->getUser() === $this) {
                $listArticle->setUser(null);
            }
        }

        return $this;
    }

    public function getProfil(): ?int
    {
        return $this->profil;
    }

    public function setProfil(int $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getRoles(){
        if($this->profil == 1){
            return ['ROLE_ADMIN'];
        }else{
            return ['ROLE_USER'];
        }
    }


    public function getSalt(){}


    public function eraseCredentials(){}

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @param mixed $request
     *
     * @return self
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function __toString(){
        return $this->surname;
    }

    /**
     * @return Collection|PostLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(PostLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(PostLike $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Quote[]
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): self
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes[] = $quote;
            $quote->setUser($this);
        }

        return $this;
    }

    public function removeQuote(Quote $quote): self
    {
        if ($this->quotes->contains($quote)) {
            $this->quotes->removeElement($quote);
            // set the owning side to null (unless already changed)
            if ($quote->getUser() === $this) {
                $quote->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

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
            $quoteLike->setUser($this);
        }

        return $this;
    }

    public function removeQuoteLike(QuoteLike $quoteLike): self
    {
        if ($this->quoteLikes->contains($quoteLike)) {
            $this->quoteLikes->removeElement($quoteLike);
            // set the owning side to null (unless already changed)
            if ($quoteLike->getUser() === $this) {
                $quoteLike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CommentLike[]
     */
    public function getCommentLikes(): Collection
    {
        return $this->commentLikes;
    }

    public function addCommentLike(CommentLike $commentLike): self
    {
        if (!$this->commentLikes->contains($commentLike)) {
            $this->commentLikes[] = $commentLike;
            $commentLike->setUser($this);
        }

        return $this;
    }

    public function removeCommentLike(CommentLike $commentLike): self
    {
        if ($this->commentLikes->contains($commentLike)) {
            $this->commentLikes->removeElement($commentLike);
            // set the owning side to null (unless already changed)
            if ($commentLike->getUser() === $this) {
                $commentLike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Star[]
     */
    public function getStars(): Collection
    {
        return $this->stars;
    }

    public function addStar(Star $star): self
    {
        if (!$this->stars->contains($star)) {
            $this->stars[] = $star;
            $star->setUser($this);
        }

        return $this;
    }

    public function removeStar(Star $star): self
    {
        if ($this->stars->contains($star)) {
            $this->stars->removeElement($star);
            // set the owning side to null (unless already changed)
            if ($star->getUser() === $this) {
                $star->setUser(null);
            }
        }

        return $this;
    }


}
