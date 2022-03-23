<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups("post:read")
     */
    private $dateCommande;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="commandes")
     * @ORM\JoinColumn(nullable=true)
     * @Groups("post:read")
     */
    private $utilisateur;





    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("post:read")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("post:read")
     */
    private $statue;

    /**
     * @ORM\OneToMany(targetEntity=LigneCommande::class, mappedBy="commande")
     */
    private $ligneCommandes;

    /**
     * @ORM\OneToOne(targetEntity=Livraisons::class, inversedBy="commande", cascade={"persist", "remove"})
     * @Groups("post:read")
     */
    private $livraison;



    public function __construct()
    {
        $this->ligneCommandes = new ArrayCollection();
    }






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(?\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;

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





    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getStatue(): ?string
    {
        return $this->statue;
    }

    public function setStatue(?string $statue): self
    {
        $this->statue = $statue;

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
            $ligneCommande->setCommande($this);
        }

        return $this;
    }

    public function removeLigneCommande(LigneCommande $ligneCommande): self
    {
        if ($this->ligneCommandes->removeElement($ligneCommande)) {
            // set the owning side to null (unless already changed)
            if ($ligneCommande->getCommande() === $this) {
                $ligneCommande->setCommande(null);
            }
        }

        return $this;
    }

    public function getLivraison(): ?Livraisons
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraisons $livraison): self
    {
        $this->livraison = $livraison;

        return $this;
    }



}
