<?php

namespace App\Entity;

use App\Repository\DanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DanceRepository::class)]
class Dance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stepsequence:read','checklist:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['stepsequence:read','checklist:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['stepsequence:read'])]
    private ?string $tact = null;

    #[ORM\Column]
    #[Groups(['stepsequence:read'])]
    private ?int $difficulty = null;

    #[ORM\Column]
    #[Groups(['stepsequence:read'])]
    private ?int $defaultBPM = null;

    /**
     * @var Collection<int, Stepsequence>
     */
    #[ORM\OneToMany(targetEntity: Stepsequence::class, mappedBy: 'dance', orphanRemoval: true)]
    private Collection $stepsequences;

    public function __construct()
    {
        $this->stepsequences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getTact(): ?string
    {
        return $this->tact;
    }

    public function setTact(string $tact): static
    {
        $this->tact = $tact;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getDefaultBPM(): ?int
    {
        return $this->defaultBPM;
    }

    public function setDefaultBPM(int $defaultBPM): static
    {
        $this->defaultBPM = $defaultBPM;

        return $this;
    }

    /**
     * @return Collection<int, Stepsequence>
     */
    public function getStepsequences(): Collection
    {
        return $this->stepsequences;
    }

    public function addStepsequence(Stepsequence $stepsequence): static
    {
        if (!$this->stepsequences->contains($stepsequence)) {
            $this->stepsequences->add($stepsequence);
            $stepsequence->setDance($this);
        }

        return $this;
    }

    public function removeStepsequence(Stepsequence $stepsequence): static
    {
        if ($this->stepsequences->removeElement($stepsequence)) {
            // set the owning side to null (unless already changed)
            if ($stepsequence->getDance() === $this) {
                $stepsequence->setDance(null);
            }
        }

        return $this;
    }
}
