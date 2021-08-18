<?php

namespace App\Entity;

use App\Repository\PatientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PatientRepository::class)
 */
class Patient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $lastname;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthdate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nir;

    /**
     * @ORM\Column(type="integer")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $completeAdress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $informationAdress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $doctorName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mutualName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mutualNumberAmc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pathology;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $trustedPerson;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

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

    public function getNir(): ?int
    {
        return $this->nir;
    }

    public function setNir(?int $nir): self
    {
        $this->nir = $nir;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
