<?php

namespace App\Entity;

use App\Repository\SendLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SendLogRepository::class)]
class SendLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $sendAt = null;

    #[ORM\ManyToOne(inversedBy: 'sendLogsId')]
    #[ORM\JoinColumn(nullable: false)]
    private ?FeedBackType $feedback = null;

    #[ORM\ManyToOne(targetEntity: Invitation::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Invitation $invitation = null;

    #[ORM\ManyToOne(targetEntity: Contact::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Contact $contact = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSendAt(): ?\DateTimeImmutable
    {
        return $this->sendAt;
    }

    public function setSendAt(\DateTimeImmutable $sendAt): self
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    public function getFeedback(): ?FeedBackType
    {
        return $this->feedback;
    }

    public function setFeedback(?FeedBackType $feedback): self
    {
        $this->feedback = $feedback;

        return $this;
    }

    /**
     * @return Invitation|null
     */
    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    /**
     * @param Invitation|null $invitation
     */
    public function setInvitation(?Invitation $invitation): void
    {
        $this->invitation = $invitation;
    }

    /**
     * @return Contact|null
     */
    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    /**
     * @param Contact|null $contact
     */
    public function setContact(?Contact $contact): void
    {
        $this->contact = $contact;
    }


}
