<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\AppointmentRepository;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=AppointmentRepository::class)
 * 
 * https://symfony.com/doc/current/doctrine/events.html#doctrine-lifecycle-callbacks
 * @ORM\HasLifecycleCallbacks()
 */
class Appointment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"appointment_get"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"appointment_get"})
     * 
     * @Assert\NotBlank
     * @Assert\Type("datetime")
     */
    private $datetimeStart;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"appointment_get"})
     * 
     * @Assert\NotBlank
     * @Assert\Type("datetime")
     */
    private $datetimeEnd;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"appointment_get"})
     */
    private $reason;

    /**
     * @ORM\ManyToOne(targetEntity=Patient::class, inversedBy="appointments")
     * @Groups({"nurse_get"})
     * @Groups({"appointment_get"})
     * 
     * @Assert\NotBlank
     * @Assert\Type(type={"object"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=Nurse::class, inversedBy="appointments")
     */
    private $nurse;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @Groups({"appointment_get"})
     * 
     * @Assert\NotBlank
     * @Assert\Length(min = 1, max = 1)
     * @Assert\Type(type={"integer"})
     * @Assert\Positive
     */
    private $status;

    public function __construct()
    {

        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetimeStart(): ?\DateTimeInterface
    {
        return $this->datetimeStart;
    }

    public function setDatetimeStart(\DateTimeInterface $datetimeStart): self
    {
        $this->datetimeStart = $datetimeStart;

        return $this;
    }

    public function getDatetimeEnd(): ?\DateTimeInterface
    {
        return $this->datetimeEnd;
    }

    public function setDatetimeEnd(\DateTimeInterface $datetimeEnd): self
    {
        $this->datetimeEnd = $datetimeEnd;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }

    public function getNurse(): ?Nurse
    {
        return $this->nurse;
    }

    public function setNurse(?Nurse $nurse): self
    {
        $this->nurse = $nurse;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Exécute cette méthode avant l'update de l'entité en BDD
     * Géré en interne par Doctrine
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new DateTime();
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
