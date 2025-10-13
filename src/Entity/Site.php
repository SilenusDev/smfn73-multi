<?php

namespace App\Entity;

use App\Enum\SiteEnum;
use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true, enumType: SiteEnum::class)]
    private ?SiteEnum $name = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $domain = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'site')]
    private Collection $user;

    /**
     * @var Collection<int, BlgArticle>
     */
    #[ORM\OneToMany(targetEntity: BlgArticle::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $blgArticles;

    /**
     * @var Collection<int, BlgCategory>
     */
    #[ORM\OneToMany(targetEntity: BlgCategory::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $blgCategories;

    /**
     * @var Collection<int, BlgSection>
     */
    #[ORM\OneToMany(targetEntity: BlgSection::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $blgSections;

    /**
     * @var Collection<int, BlgComment>
     */
    #[ORM\OneToMany(targetEntity: BlgComment::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $blgComments;

    /**
     * @var Collection<int, BlgLike>
     */
    #[ORM\OneToMany(targetEntity: BlgLike::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $blgLikes;

    /**
     * @var Collection<int, BlgPage>
     */
    #[ORM\OneToMany(targetEntity: BlgPage::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $blgPages;

    /**
     * @var Collection<int, BlgTag>
     */
    #[ORM\OneToMany(targetEntity: BlgTag::class, mappedBy: 'site', orphanRemoval: true)]
    private Collection $blgTags;

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->blgArticles = new ArrayCollection();
        $this->blgCategories = new ArrayCollection();
        $this->blgSections = new ArrayCollection();
        $this->blgComments = new ArrayCollection();
        $this->blgLikes = new ArrayCollection();
        $this->blgPages = new ArrayCollection();
        $this->blgTags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?SiteEnum
    {
        return $this->name;
    }

    public function setName(SiteEnum $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retourne le code du site (slns, nsdm)
     */
    public function getCode(): ?string
    {
        return $this->name?->getCode();
    }

    /**
     * Retourne le nom d'affichage du site
     */
    public function getDisplayName(): ?string
    {
        return $this->name?->getDisplayName();
    }

    /**
     * Retourne le préfixe de route du site
     */
    public function getRoutePrefix(): ?string
    {
        return $this->name?->getRoutePrefix();
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setSite($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSite() === $this) {
                $user->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgArticle>
     */
    public function getBlgArticles(): Collection
    {
        return $this->blgArticles;
    }

    public function addBlgArticle(BlgArticle $blgArticle): static
    {
        if (!$this->blgArticles->contains($blgArticle)) {
            $this->blgArticles->add($blgArticle);
            $blgArticle->setSite($this);
        }

        return $this;
    }

    public function removeBlgArticle(BlgArticle $blgArticle): static
    {
        if ($this->blgArticles->removeElement($blgArticle)) {
            if ($blgArticle->getSite() === $this) {
                $blgArticle->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgCategory>
     */
    public function getBlgCategories(): Collection
    {
        return $this->blgCategories;
    }

    public function addBlgCategory(BlgCategory $blgCategory): static
    {
        if (!$this->blgCategories->contains($blgCategory)) {
            $this->blgCategories->add($blgCategory);
            $blgCategory->setSite($this);
        }

        return $this;
    }

    public function removeBlgCategory(BlgCategory $blgCategory): static
    {
        if ($this->blgCategories->removeElement($blgCategory)) {
            if ($blgCategory->getSite() === $this) {
                $blgCategory->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgSection>
     */
    public function getBlgSections(): Collection
    {
        return $this->blgSections;
    }

    public function addBlgSection(BlgSection $blgSection): static
    {
        if (!$this->blgSections->contains($blgSection)) {
            $this->blgSections->add($blgSection);
            $blgSection->setSite($this);
        }

        return $this;
    }

    public function removeBlgSection(BlgSection $blgSection): static
    {
        if ($this->blgSections->removeElement($blgSection)) {
            if ($blgSection->getSite() === $this) {
                $blgSection->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgComment>
     */
    public function getBlgComments(): Collection
    {
        return $this->blgComments;
    }

    public function addBlgComment(BlgComment $blgComment): static
    {
        if (!$this->blgComments->contains($blgComment)) {
            $this->blgComments->add($blgComment);
            $blgComment->setSite($this);
        }

        return $this;
    }

    public function removeBlgComment(BlgComment $blgComment): static
    {
        if ($this->blgComments->removeElement($blgComment)) {
            if ($blgComment->getSite() === $this) {
                $blgComment->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgLike>
     */
    public function getBlgLikes(): Collection
    {
        return $this->blgLikes;
    }

    public function addBlgLike(BlgLike $blgLike): static
    {
        if (!$this->blgLikes->contains($blgLike)) {
            $this->blgLikes->add($blgLike);
            $blgLike->setSite($this);
        }

        return $this;
    }

    public function removeBlgLike(BlgLike $blgLike): static
    {
        if ($this->blgLikes->removeElement($blgLike)) {
            if ($blgLike->getSite() === $this) {
                $blgLike->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgPage>
     */
    public function getBlgPages(): Collection
    {
        return $this->blgPages;
    }

    public function addBlgPage(BlgPage $blgPage): static
    {
        if (!$this->blgPages->contains($blgPage)) {
            $this->blgPages->add($blgPage);
            $blgPage->setSite($this);
        }

        return $this;
    }

    public function removeBlgPage(BlgPage $blgPage): static
    {
        if ($this->blgPages->removeElement($blgPage)) {
            if ($blgPage->getSite() === $this) {
                $blgPage->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgTag>
     */
    public function getBlgTags(): Collection
    {
        return $this->blgTags;
    }

    public function addBlgTag(BlgTag $blgTag): static
    {
        if (!$this->blgTags->contains($blgTag)) {
            $this->blgTags->add($blgTag);
            $blgTag->setSite($this);
        }

        return $this;
    }

    public function removeBlgTag(BlgTag $blgTag): static
    {
        if ($this->blgTags->removeElement($blgTag)) {
            if ($blgTag->getSite() === $this) {
                $blgTag->setSite(null);
            }
        }

        return $this;
    }
}
