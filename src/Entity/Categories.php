<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=CategoriesRepository::class)
 */
class Categories
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="*donnez le nom svp")
     */
    private $nom;

    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="*remplir le champ description svp")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Produits::class, mappedBy="categorie")
     */
    private $produits;

    /**
    * @ORM\Column(type="string", length=255)
     *@Groups("post:read")
    * @var string|null
     */
    private $imageName;
    /**
     * @Vich\UploadableField(mapping="property_image", fileNameProperty="imageName")
     * @var File|null
     */
    private $imageFile;


    public function __construct()
    {
        $this->produits = new ArrayCollection();
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
     * @return Collection|Produits[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produits $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setCategorie($this);
        }

        return $this;
    }

    public function removeProduit(Produits $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getCategorie() === $this) {
                $produit->setCategorie(null);
            }
        }

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
}
