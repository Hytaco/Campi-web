<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Utilisateur;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 */
class Commentaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="commentaires")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Locaux::class, inversedBy="commentaires")
     */
    private $locaux;

    /**
     * @ORM\OneToMany(targetEntity=Postlik::class, mappedBy="post")
     */
    private $lik;

    public function __construct()
    {
        $this->lik = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): ?Utilisateur
    {
        return $this->user;
    }

    public function setUser(?Utilisateur $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getLocaux(): ?Locaux
    {
        return $this->locaux;
    }

    public function setLocaux(?Locaux $locaux): self
    {
        $this->locaux = $locaux;

        return $this;
    }

    /**
     * @return Collection|Postlik[]
     */
    public function getLik(): Collection
    {
        return $this->lik;
    }

    public function addLik(Postlike $lik): self
    {
        if (!$this->lik->contains($lik)) {
            $this->lik[] = $lik;
            $lik->setPost($this);
        }

        return $this;
    }

    public function removeLik(Postlike $lik): self
    {
        if ($this->lik->removeElement($lik)) {
            // set the owning side to null (unless already changed)
            if ($lik->getPost() === $this) {
                $lik->setPost(null);
            }
        }

        return $this;
    }
    /**
     * permet de savaoir si cet article est like par un utilisateur
     * @param Utilisateur $user
     * @return boolean
     */
    public function islikedByUser(Utilisateur $user ):bool{
        foreach ($this->lik as $lik)
        {
            if ($lik->getUser() == $user)
                return true;
        }
        return false;

    }

}
