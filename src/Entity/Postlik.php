<?php

namespace App\Entity;

use App\Repository\PostlikRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PostlikRepository::class)
 */
class Postlik
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Commentaire::class, inversedBy="likes")
     */
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="likes")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Commentaire
    {
        return $this->post;
    }

    public function setPost(?Commentaire $post): self
    {
        $this->post = $post;

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
}
