<?php

namespace App\Entity;

use App\Repository\LocauxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 *  * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=LocauxRepository::class)
 */
class Locaux
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Groups("post:read")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Nom obligatoire")
     *  @Groups("post:read")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Adresse obligatoire")
     * @Groups("post:read")
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="Description obligatoire")
     *  @Groups("post:read")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string|null
     *  @Groups("post:read")
     */
    private $imageName;
    /**
     * @Vich\UploadableField(mapping="property_image", fileNameProperty="imageName")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *  @Groups("post:read")
     */
    private $note;

    /**
     * @ORM\OneToMany(targetEntity=Commentaire::class, mappedBy="locaux")
     */
    private $commentaires;

    /**
     * @ORM\OneToMany(targetEntity=Programmes::class, mappedBy="locale")
     */
    private $programmes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *  @Groups("post:read")
     */
    private $googleMap;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->programmes = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection|Commentaire[]
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setManyToOne($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getManyToOne() === $this) {
                $commentaire->setManyToOne(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Programmes[]
     */
    public function getProgrammes(): Collection
    {
        return $this->programmes;
    }

    public function addProgramme(Programmes $programme): self
    {
        if (!$this->programmes->contains($programme)) {
            $this->programmes[] = $programme;
            $programme->setLocale($this);
        }

        return $this;
    }

    public function removeProgramme(Programmes $programme): self
    {
        if ($this->programmes->removeElement($programme)) {
            // set the owning side to null (unless already changed)
            if ($programme->getLocale() === $this) {
                $programme->setLocale(null);
            }
        }

        return $this;
    }

    public function getGoogleMap(): ?string
    {
        return $this->googleMap;
    }

    public function setGoogleMap(?string $googleMap): self
    {
        $this->googleMap = $googleMap;

        return $this;
    }

}




