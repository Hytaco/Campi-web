<?php

namespace App\Entity;

use App\Entity\Utilisateur;
use App\Repository\EvenementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=EvenementsRepository::class)
 */
class Evenements
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
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="nom est obligatoire")
     */
    private $nom;



    /**
     * @Groups("post:read")
     * @ORM\Column(type="date")
     */
    private $date;

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
     * @Groups("post:read")
     * @ORM\Column(type="integer")
     */
    private $nbrplace;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="date")
     */
    private $datef;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=50)
     */
    private $Lieu;


    /**
     * @ORM\OneToMany(targetEntity=PostLikes::class, mappedBy="post", cascade={"persist", "remove"})
     */
    private $like;

    /**
     * @ORM\ManyToMany(targetEntity=Sponsors::class, inversedBy="evenements")
     */
    private $sponsors;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;


    public function __construct()
    {
        $this->likes = new ArrayCollection();
        $this->sponsors = new ArrayCollection();
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


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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



    public function getNbrplace(): ?int
    {
        return $this->nbrplace;
    }

    public function setNbrplace(int $nbrplace): self
    {
        $this->nbrplace = $nbrplace;

        return $this;
    }

    public function getDatef(): ?\DateTimeInterface
    {
        return $this->datef;
    }

    public function setDatef(\DateTimeInterface $datef): self
    {
        $this->datef = $datef;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->Lieu;
    }

    public function setLieu(string $Lieu): self
    {
        $this->Lieu = $Lieu;

        return $this;
    }

    /**
     * @return Collection|PostLikes[]
     */
    public function getLikes(): Collection
    {
        return $this->like;
    }

    public function addLike(PostLikes $like): self
    {
        if (!$this->like->contains($like)) {
            $this->like[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(PostLikes $like): self
    {
        if ($this->like->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @param Utilisateur $user
     * @return boolean
     */
    public function isLikedByUser(Utilisateur $user):bool{

        foreach ($this->like as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection|Sponsors[]
     */
    public function getSponsors(): Collection
    {
        return $this->sponsors;
    }

    public function addSponsor(Sponsors $sponsor): self
    {
        if (!$this->sponsors->contains($sponsor)) {
            $this->sponsors[] = $sponsor;
        }

        return $this;
    }

    public function removeSponsor(Sponsors $sponsor): self
    {
        $this->sponsors->removeElement($sponsor);

        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }



}
