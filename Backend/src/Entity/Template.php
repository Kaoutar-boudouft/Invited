<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $html = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $css = null;

    #[ORM\ManyToOne(inversedBy: 'templateId')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'template', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $invitationsId;

    #[ORM\Column(length: 255)]
    private ?string $title = null;


    public function __construct()
    {
        $this->invitationsId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $invitationId->setTemplate($this);
        }

        return $this;
    }

    public function removeInvitationId(Invitation $invitationId): self
    {
        if ($this->invitationsId->removeElement($invitationId)) {
            // set the owning side to null (unless already changed)
            if ($invitationId->getTemplate() === $this) {
                $invitationId->setTemplate(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCss(): ?string
    {
        return $this->css;
    }

    public function setCss(?string $css): self
    {
        $this->css = $css;

        return $this;
    }
}
