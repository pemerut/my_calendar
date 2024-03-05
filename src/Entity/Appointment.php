<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: "Please enter a date and time.")]
    #[Assert\DateTime(message: "Please enter a valid date and time.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "The name cannot be blank.")]
    #[Assert\Length(
        max: 100,
        maxMessage: "The name cannot be longer than {{ limit }} characters."
    )]
    private ?string $patientName = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "The email cannot be blank.")]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "The phone number cannot be blank.")]
    #[Assert\Regex(
        pattern: "/^\+?[0-9. ()-]{7,}$/",
        message: "Please enter a valid phone number."
    )]
    private ?string $phone = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getPatientName(): ?string
    {
        return $this->patientName;
    }

    public function setPatientName(string $patientName): self
    {
        $this->patientName = $patientName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }
}
