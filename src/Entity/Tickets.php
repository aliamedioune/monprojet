<?php

namespace App\Entity;

use App\Repository\TicketsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

#[ORM\Entity(repositoryClass: TicketsRepository::class)]
#[ApiResource]
class Tickets
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $ticketsLeft;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: Winners::class)]
    private $winners;

    public function __construct()
    {
        $this->winners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTicketsLeft(): ?int
    {
        return $this->ticketsLeft;
    }

    public function setTicketsLeft(?int $ticketsLeft): self
    {
        $this->ticketsLeft = $ticketsLeft;

        return $this;
    }

    /**
     * @return Collection<int, Winners>
     */
    public function getWinners(): Collection
    {
        return $this->winners;
    }

    public function addWinner(Winners $winner): self
    {
        if (!$this->winners->contains($winner)) {
            $this->winners[] = $winner;
            $winner->setTicket($this);
        }

        return $this;
    }

    public function removeWinner(Winners $winner): self
    {
        if ($this->winners->removeElement($winner)) {
            // set the owning side to null (unless already changed)
            if ($winner->getTicket() === $this) {
                $winner->setTicket(null);
            }
        }

        return $this;
    }
}
