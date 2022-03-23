<?php

namespace App\Entity;

use App\Repository\ProgrammesRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntityValidator;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProgrammesRepository::class)
 */
class Programmes
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("post:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Nom obligatoire")
     * @Groups("post:read")
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today")
     * @Groups("post:read")
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(message="Duree obligatoire")
     * @Groups("post:read")
     */
    private $duree;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\NotBlank(message="Details obligatoire")
     * @Groups("post:read")
     */
    private $details;

    /**
     * @ORM\ManyToOne(targetEntity=Transporteur::class, inversedBy="programmes")
     */
    private $transporteur;

    /**
     * @ORM\OneToMany(targetEntity=Alerts::class, mappedBy="programme")
     */
    private $alerts;

    /**
     *  @ORM\ManyToOne(targetEntity=Locaux::class, inversedBy="programmes")
     *  @ORM\JoinColumn(name="locale_id", referencedColumnName="id", nullable=true)
     *  @Groups("post:read")
     */
    private $locale;

    /**
     * @ORM\ManyToMany(targetEntity=Utilisateur::class, inversedBy="programmes")
     */
    private $participants;

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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getTransporteur(): ?Transporteur
    {
        return $this->transporteur;
    }

    public function setTransporteur(?Transporteur $transporteur): self
    {
        $this->transporteur = $transporteur;

        return $this;
    }






    public function __construct()
    {
        $this->alerts = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }



    /**
     * @return Collection|Alerts[]
     */
    public function getAlerts(): Collection
    {
        return $this->alerts;
    }

    public function addAlert(Alerts $alert): self
    {
        if (!$this->alerts->contains($alert)) {
            $this->alerts[] = $alert;
            $alert->setProgramme($this);
        }

        return $this;
    }

    public function removeAlert(Alerts $alert): self
    {
        if ($this->alerts->removeElement($alert)) {
            // set the owning side to null (unless already changed)
            if ($alert->getProgramme() === $this) {
                $alert->setProgramme(null);
            }
        }

        return $this;
    }

    public function getLocale(): ?Locaux
    {
        return $this->locale;
    }

    public function setLocale(?Locaux $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Utilisateur $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Utilisateur $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }
}
