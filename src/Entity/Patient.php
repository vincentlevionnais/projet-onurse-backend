<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PatientRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PatientRepository::class)
 * Cette entité va réagir aux évènements "lifecycle callbacks" de Doctrine
 * @ORM\HasLifecycleCallbacks()
 */
class Patient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"patients_get"})
     * @Groups ({"appointment_get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"patients_get"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"patients_get"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"patients_get"}
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patients_get"})
     */
    private $nir;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"patients_get"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"patients_get"})
     */
    private $completeAdress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patients_get"})
     */
    private $informationAdress;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"patients_get"})
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patients_get"})
     */
    private $doctorName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patients_get"})
     */
    private $mutualName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patients_get"})
     */
    private $mutualNumberAmc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patients_get"})
     */
    private $pathology;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"patients_get"})
     */
    private $trustedPerson;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Nurse::class, inversedBy="patients")
     */
    private $nurse;

    /**
     * @ORM\OneToMany(targetEntity=Appointment::class, mappedBy="patient",  cascade={"remove"})
     */
    private $appointments;

    public function __construct()
    {
        $this->appointments = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(?\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getNir(): ?string
    {
        return $this->nir;
    }

    public function setNir(?string $nir): self
    {
        $this->nir = $nir;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCompleteAdress(): ?string
    {
        return $this->completeAdress;
    }

    public function setCompleteAdress(string $completeAdress): self
    {
        $this->completeAdress = $completeAdress;

        return $this;
    }

    public function getInformationAdress(): ?string
    {
        return $this->informationAdress;
    }

    public function setInformationAdress(?string $informationAdress): self
    {
        $this->informationAdress = $informationAdress;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getDoctorName(): ?string
    {
        return $this->doctorName;
    }

    public function setDoctorName(?string $doctorName): self
    {
        $this->doctorName = $doctorName;

        return $this;
    }

    public function getMutualName(): ?string
    {
        return $this->mutualName;
    }

    public function setMutualName(?string $mutualName): self
    {
        $this->mutualName = $mutualName;

        return $this;
    }

    public function getMutualNumberAmc(): ?string
    {
        return $this->mutualNumberAmc;
    }

    public function setMutualNumberAmc(?string $mutualNumberAmc): self
    {
        $this->mutualNumberAmc = $mutualNumberAmc;

        return $this;
    }

    public function getPathology(): ?string
    {
        return $this->pathology;
    }

    public function setPathology(?string $pathology): self
    {
        $this->pathology = $pathology;

        return $this;
    }

    public function getTrustedPerson(): ?string
    {
        return $this->trustedPerson;
    }

    public function setTrustedPerson(?string $trustedPerson): self
    {
        $this->trustedPerson = $trustedPerson;

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

    public function getNurse(): ?Nurse
    {
        return $this->nurse;
    }

    public function setNurse(?Nurse $nurse): self
    {
        $this->nurse = $nurse;

        return $this;
    }

    /**
     * @return Collection|Appointment[]
     */
    public function getAppointments(): Collection
    {
        return $this->appointments;
    }

    public function addAppointment(Appointment $appointment): self
    {
        if (!$this->appointments->contains($appointment)) {
            $this->appointments[] = $appointment;
            $appointment->setPatient($this);
        }

        return $this;
    }

    public function removeAppointment(Appointment $appointment): self
    {
        if ($this->appointments->removeElement($appointment)) {
            // set the owning side to null (unless already changed)
            if ($appointment->getPatient() === $this) {
                $appointment->setPatient(null);
            }
        }

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
}
