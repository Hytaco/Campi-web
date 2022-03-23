<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\PostlikeRepository;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass=ProduitsRepository::class)
 */
class Produits
{
    /**
     * @ORM\Id
     *@Groups("post:read")
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="*nom est obligatoire")
     */
    private $nom;

    /**
     *@Groups("post:read")
     * @Assert\NotBlank(message="*donnez le prix svp")
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     *@Groups("post:read")
     * @Assert\NotBlank(message="*remplir le champ description svp ")
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     *@Groups("post:read")
     * @Assert\NotBlank(message="*quantite d'une produit est obligatoire")
     * @ORM\Column(type="integer")
     */
    private $quantite;

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

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class)
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="produits")
     */
    private $utilisateur;

    /**
     * @ORM\OneToMany(targetEntity=LigneCommande::class, mappedBy="produit")
     */
    private $ligneCommandes;

    /**
     * @ORM\OneToMany(targetEntity=Postlike::class, mappedBy="post")
     */
    private $like;

    public function __construct()
    {
        $this->categorie = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->ligneCommandes = new ArrayCollection();

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


    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

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

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

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

    public function getCategorie()
    {
        return $this->categorie;
    }

    public function setCategorie(?Categories $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }


    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection|LigneCommande[]
     */
    public function getLigneCommandes(): Collection
    {
        return $this->ligneCommandes;
    }

    public function addLigneCommande(LigneCommande $ligneCommande): self
    {
        if (!$this->ligneCommandes->contains($ligneCommande)) {
            $this->ligneCommandes[] = $ligneCommande;
            $ligneCommande->setProduit($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getProduit() === $this) {
                $ligneCommande->setProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Postlike[]
     */
    public function getLike(): Collection
    {
        return $this->like;
    }

    public function addLike(Postlike $like): self
    {
        if (!$this->like->contains($like)) {
            $this->like[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Postlike $like): self
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
     * permet de savaoir si cet article est like par un utilisateur
     * @param Utilisateur $user
     * @return boolean
     */
    public function islikedByUser(Utilisateur $user ):bool{
        foreach ($this->like as $like)
        {
            if ($like->getUser() == $user)
                return true;
        }
        return false;

    }
}
