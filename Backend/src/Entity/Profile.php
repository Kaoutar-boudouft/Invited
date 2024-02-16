<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTimeInterface $dob = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $profile_picture = null;

    #[ORM\Column(nullable: true)]
    private ?int $phoneNumber = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $emailVerifiedAt = null;

    /**
     * @return DateTimeImmutable|null
     */
    public function getEmailVerifiedAt(): ?DateTimeImmutable
    {
        return $this->emailVerifiedAt;
    }

    /**
     * @param DateTimeImmutable|null $emailVerifiedAt
     */
    public function setEmailVerifiedAt(?DateTimeImmutable $emailVerifiedAt): void
    {
        $this->emailVerifiedAt = $emailVerifiedAt;
    }

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $phoneVerifiedAt = null;

    /**
     * @return DateTimeImmutable|null
     */
    public function getPhoneVerifiedAt(): ?DateTimeImmutable
    {
        return $this->phoneVerifiedAt;
    }

    /**
     * @param DateTimeImmutable|null $phoneVerifiedAt
     */
    public function setPhoneVerifiedAt(?DateTimeImmutable $phoneVerifiedAt): void
    {
        $this->phoneVerifiedAt = $phoneVerifiedAt;
    }


    #[ORM\OneToOne(inversedBy: 'profileId', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getDob(): ?DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(?DateTimeInterface $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->profile_picture;
    }

    public function setPicture(?string $profile_picture): self
    {
        $this->profile_picture = $profile_picture;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?int $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
