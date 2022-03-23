<?php

namespace App\Entity;

use App\Repository\AlertsRepository;
use Doctrine\ORM\Mapping as ORM;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints as CaptchaAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AlertsRepository::class)
 */
class Alerts
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=50)
     *@Groups("post:read")
     * @Assert\NotBlank(message="localisation obligatoire")
     */
    private $localisation;

    /**
     * @ORM\Column(type="date")
     */
    private $date;


    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=255)
     */
    private $rapport;



    /**
     *@Groups("post:read")
     * @ORM\Column(type="integer")
     * @Assert\Length(
     *     min = 8,
     *     max = 8,
     *     minMessage = " les nombres doivent être supérieurs à 8 chiffres",
     *     maxMessage = " les nombres doivent être inférieurs à 8 chiffres",
     * )
     * @Assert\NotBlank(message="Le numéro de telephonne doit etre à 8 chiffres")
     */
    private $telephone;


    /**
     *@Groups("post:read")
     * @ORM\Column(type="string", length=50)
     *   @Assert\Email(
     *     message = "l' email '{{ value }}' n'est pas valid"
     * )
     * @Assert\NotBlank(message="mail obligatoire")

     */
    private $mail;

    /**
     * @ORM\ManyToOne(targetEntity=Programmes::class, inversedBy="alerts")
     */
    private $programme;
    /**
     * @CaptchaAssert\ValidCaptcha(
     *      message = "CAPTCHA validation failed, try again."
     * )
     */
    protected $captchaCode;

    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

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

    public function getRapport(): ?string
    {
        return $this->rapport;
    }

    public function setRapport(string $rapport): self
    {
        $this->rapport = $rapport;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getProgramme(): ?Programmes
    {
        return $this->programme;
    }

    public function setProgramme(?Programmes $programme): self
    {
        $this->programme = $programme;

        return $this;
    }
}
