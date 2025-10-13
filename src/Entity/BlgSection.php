<?php

namespace App\Entity;

use App\Entity\Traits\ParentTrait;
use App\Entity\Traits\SeoTrait;
use App\Entity\Traits\TitleTrait;
use App\Repository\BlgSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlgSectionRepository::class)]
class BlgSection
{
    use TitleTrait;
    use SeoTrait;
    use ParentTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blgSections')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    /**
     * @var Collection<int, BlgTag>
     */
    #[ORM\ManyToMany(targetEntity: BlgTag::class, inversedBy: 'sections')]
    private Collection $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Collection<int, BlgTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(BlgTag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(BlgTag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
