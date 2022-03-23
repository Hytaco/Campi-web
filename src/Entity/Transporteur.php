<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints\UniqueValidator;
use App\Repository\TransporteurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Unique;
use Symfony\Component\Validator\Constraints\CountValidator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransporteurRepository::class)
 */
class Transporteur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     *@Groups("post:read")
     * @ORM\Column(type="integer")
     * @Assert\Length(
     *     min = 8,
     *     max = 8,
     *     minMessage = " les nombres doivent être supérieurs à 8 chiffres",
     *     maxMessage = " les nombres doivent être inférieurs à 8 chiffres",
     * )
     */
    private $numero;

    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=50)
     */
    private $adresse;
    /**
     *@Groups("post:read")
     * @ORM\Entity
     * @UniqueEntity("mail")
     */
    /**
     * @ORM\Column(name = "mail", type="string", length=50 , unique=true)
     * @Assert\Email
     */
    private $mail;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

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
