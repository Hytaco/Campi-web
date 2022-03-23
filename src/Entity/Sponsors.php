<?php

namespace App\Entity;

use App\Repository\SponsorsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=SponsorsRepository::class)
 */
class Sponsors
{
    /**
     * @ORM\Id
     *@Groups("post:read")
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")

     */
    private $id;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="nom est obligatoire")
     */
    private $nom;



    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=50)
     */
    private $adresse;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="nom est obligatoire")
     * @Assert\Email(message = "The email '{{ value }}' is not a valid
    email.")
     */
    private $mail;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
     */
    private $imageName;
    /**
     * @Vich\UploadableField(mapping="property_image", fileNameProperty="imageName")
     * @var File|null
     */
    private $imageFile;


    /**
     * @ORM\ManyToMany(targetEntity=Evenements::class, mappedBy="sponsors")
     */
    private $evenements;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
    }






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

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param string|null $imageName
     */
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
    }

    /**
     * @return Collection|Evenements[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenements $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->addSponsor($this);
        }

        return $this;
    }

    public function removeEvenement(Evenements $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            $evenement->removeSponsor($this);
        }

        return $this;
    }







}
