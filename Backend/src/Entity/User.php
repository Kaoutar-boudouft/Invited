<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $userName = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Profile $profileId = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Template::class, orphanRemoval: true)]
    private Collection $templatesId;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $invitationsId;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Contact::class, orphanRemoval: true)]
    private Collection $contactsId;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $accountVerifiedAt = null;

    public function __construct()
    {
        $this->templatesId = new ArrayCollection();
        $this->invitationsId = new ArrayCollection();
        $this->contactsId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->userName;
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getProfileId(): ?Profile
    {
        return $this->profileId;
    }

    public function setProfileId(Profile $profileId): self
    {
        // set the owning side of the relation if necessary
        if ($profileId->getUser() !== $this) {
            $profileId->setUser($this);
        }

        $this->profileId = $profileId;

        return $this;
    }

    /**
     * @return Collection<int, Template>
     */
    public function getTemplatesId(): Collection
    {
        return $this->templatesId;
    }

    public function addTemplateId(Template $templateId): self
    {
        if (!$this->templatesId->contains($templateId)) {
            $this->templatesId->add($templateId);
            $templateId->setUser($this);
        }

        return $this;
    }

    public function removeTemplateId(Template $templateId): self
    {
        if ($this->templatesId->removeElement($templateId)) {
            // set the owning side to null (unless already changed)
            if ($templateId->getUser() === $this) {
                $templateId->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getInvitationsId(): Collection
    {
        return $this->invitationsId;
    }

    public function addInvitationId(Invitation $invitationId): self
    {
        if (!$this->invitationsId->contains($invitationId)) {
            $this->invitationsId->add($invitationId);
            $invitationId->setUser($this);
        }

        return $this;
    }

    public function removeInvitationId(Invitation $invitationId): self
    {
        if ($this->invitationsId->removeElement($invitationId)) {
            // set the owning side to null (unless already changed)
            if ($invitationId->getUser() === $this) {
                $invitationId->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContactsId(): Collection
    {
        return $this->contactsId;
    }

    public function addContactsId(Contact $contactsId): self
    {
        if (!$this->contactsId->contains($contactsId)) {
            $this->contactsId->add($contactsId);
            $contactsId->setUser($this);
        }

        return $this;
    }

    public function removeContactsId(Contact $contactsId): self
    {
        if ($this->contactsId->removeElement($contactsId)) {
            // set the owning side to null (unless already changed)
            if ($contactsId->getUser() === $this) {
                $contactsId->setUser(null);
            }
        }

        return $this;
    }

    public function getAccountVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->accountVerifiedAt;
    }

    public function setAccountVerifiedAt(?\DateTimeImmutable $accountVerifiedAt): self
    {
        $this->accountVerifiedAt = $accountVerifiedAt;

        return $this;
    }
}
