<?php

namespace App\Entity;

use App\Repository\StepsequenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: StepsequenceRepository::class)]
class Stepsequence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['stepsequence:read','checklist:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['stepsequence:read','checklist:read'])]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['stepsequence:read','checklist:read'])]
    private ?int $difficulty = null;

    /**
     * @var Collection<int, Checklist>
     */
    #[ORM\ManyToMany(targetEntity: Checklist::class, inversedBy: 'stepsequences')]
    private Collection $checklists;

    #[ORM\ManyToOne(inversedBy: 'stepsequences')]
    #[Groups(['stepsequence:read','checklist:read'])]
    private ?Badge $badge = null;

    #[ORM\ManyToOne(inversedBy: 'stepsequences')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stepsequence:read','checklist:read'])]
    private ?Dance $dance = null;



    /**
     * @var Collection<int, Step>
     */
    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'stepsequence')]
    #[Groups(['stepsequence:read'])]
    private Collection $steps;



    public function __construct()
    {
        $this->checklists = new ArrayCollection();
        $this->steps = new ArrayCollection();
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

    /**
     * @return Collection<int, Checklist>
     */
    public function getChecklists(): Collection
    {
        return $this->checklists;
    }

    public function addChecklist(Checklist $checklist): static
    {
        if (!$this->checklists->contains($checklist)) {
            $this->checklists->add($checklist);
        }

        return $this;
    }

    public function removeChecklist(Checklist $checklist): static
    {
        $this->checklists->removeElement($checklist);

        return $this;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBadge(?Badge $badge): static
    {
        $this->badge = $badge;

        return $this;
    }

    public function getDance(): ?Dance
    {
        return $this->dance;
    }

    public function setDance(?Dance $dance): static
    {
        $this->dance = $dance;

        return $this;
    }

    /**
     * @return Collection<int, Step>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addHowquick(Step $howquick): static
    {
        if (!$this->steps->contains($howquick)) {
            $this->steps->add($howquick);
            $howquick->setStepsequence($this);
        }

        return $this;
    }

    public function removeHowquick(Step $howquick): static
    {
        if ($this->steps->removeElement($howquick)) {
            // set the owning side to null (unless already changed)
            if ($howquick->getStepsequence() === $this) {
                $howquick->setStepsequence(null);
            }
        }

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(?int $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }
}
