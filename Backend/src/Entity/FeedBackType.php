<?php

namespace App\Entity;

use App\Repository\FeedBackTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedBackTypeRepository::class)]
class FeedBackType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\OneToMany(mappedBy: 'feedback', targetEntity: SendLog::class, orphanRemoval: true)]
    private Collection $sendLogsId;

    public function __construct()
    {
        $this->sendLogsId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return Collection<int, SendLog>
     */
    public function getSendLogsId(): Collection
    {
        return $this->sendLogsId;
    }

    public function addSendLogsId(SendLog $sendLogsId): self
    {
        if (!$this->sendLogsId->contains($sendLogsId)) {
            $this->sendLogsId->add($sendLogsId);
            $sendLogsId->setFeedback($this);
        }

        return $this;
    }

    public function removeSendLogsId(SendLog $sendLogsId): self
    {
        if ($this->sendLogsId->removeElement($sendLogsId)) {
            // set the owning side to null (unless already changed)
            if ($sendLogsId->getFeedback() === $this) {
                $sendLogsId->setFeedback(null);
            }
        }

        return $this;
    }
}
