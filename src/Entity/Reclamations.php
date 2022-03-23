<?php

namespace App\Entity;

use App\Repository\ReclamationsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReclamationsRepository::class)
 */
class Reclamations
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
     * @ORM\Column(type="string", length=50)
     * * @Assert\Length(
     *      min = 10,
     *      max = 50,
     *      minMessage = "minimum 10 caracteres!",
     *      maxMessage = "maximum 50 caracteres!"
     * )
     */
    private $description;

    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
