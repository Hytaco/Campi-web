<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity ;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @Vich\Uploadable
 * @UniqueEntity(
 * fields = {"email"},
 * message = " ce mail est déja utilisé !"
 * )
 */
class Utilisateur implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="l' email ne doit pas être vide")
     * @Groups("post:read")
     *   @Assert\Email(
     *     message = "l' email '{{ value }}' n'est pas valid"
     * )
     */
    private $email;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @Groups("post:read")
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(min="6",minMessage="Votre mot de passe doit etre superieur a 6 caractéres")
     * @Assert\EqualTo(propertyPath="confirmPassword",message="Votre mot de passe doit etre identitique au mot de passe de confirmation")
     */
    private $password;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Adresse;
    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Telephone;

    /**
     * @Groups("post:read")
     * @Assert\NotBlank(message="le nom ne doit pas etre vide")
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @Groups("post:read")
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="le prenom ne doit pas etre vide")
     */
    private $prenom;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="utilisateur")
     */
    private $commandes;

    /**
     * @ORM\OneToMany(targetEntity=Produits::class, mappedBy="utilisateur")
     */
    private $produits;

    /**
     * @Assert\EqualTo(propertyPath="password",
     *     message = " Vous n'avez pas tapez le meme password")
     */
    public $confirmPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activationToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $change_token;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $githubId;

public $captcha;

/**
 * @ORM\Column(type="string", length=255, nullable=true)
 */
private $googleId;



    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->produits = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return (string) $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(?string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->Telephone;
    }

    public function setTelephone(?int $Telephone): self
    {
        $this->Telephone = $Telephone;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }
    public function serialize() {

        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));

    }

    public function unserialize($serialized) {

        list (
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($serialized);
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }

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
            $produit->setUtilisateur($this);
        }

        return $this;
    }

    public function removeProduit(Produits $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getUtilisateur() === $this) {
                $produit->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    /**
     * @param string|null $activationToken
     * @return $this
     */
    public function setActivationToken( $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }

public function isVerified () : bool{
        return($this->activationToken==null);
}

public function getChangeToken(): ?string
{
    return $this->change_token;
}

public function setChangeToken(?string $change_token): self
{
    $this->change_token = $change_token;

    return $this;
}

public function getGithubId(): ?int
{
    return $this->githubId;
}

public function setGithubId(?int $githubId): self
{
    $this->githubId = $githubId;

    return $this;
}

public function getGoogleId(): ?string
{
    return $this->googleId;
}

public function setGoogleId(?string $googleId): self
{
    $this->googleId = $googleId;

    return $this;
}

}
