<?php

namespace App\Entity;

use App\Repository\PropositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PropositionRepository::class)
 */
class Proposition
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=20)
     */
    private $nom;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="integer")
     */
    private $nombrePlace;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=20)
     */
    private $email;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=50)
     */
    private $mail;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getNombrePlace(): ?int
    {
        return $this->nombrePlace;
    }

    public function setNombrePlace(int $nombrePlace): self
    {
        $this->nombrePlace = $nombrePlace;

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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }
}
