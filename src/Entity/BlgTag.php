<?php

namespace App\Entity;

use App\Entity\Traits\SeoTrait;
use App\Entity\Traits\TitleTrait;
use App\Repository\BlgTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlgTagRepository::class)]
class BlgTag
{
    use TitleTrait;
    use SeoTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blgTags')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    /**
     * @var Collection<int, BlgArticle>
     */
    #[ORM\ManyToMany(targetEntity: BlgArticle::class, mappedBy: 'tags')]
    private Collection $articles;

    /**
     * @var Collection<int, BlgPage>
     */
    #[ORM\ManyToMany(targetEntity: BlgPage::class, mappedBy: 'tags')]
    private Collection $pages;

    /**
     * @var Collection<int, BlgCategory>
     */
    #[ORM\ManyToMany(targetEntity: BlgCategory::class, mappedBy: 'tags')]
    private Collection $categories;

    /**
     * @var Collection<int, BlgSection>
     */
    #[ORM\ManyToMany(targetEntity: BlgSection::class, mappedBy: 'tags')]
    private Collection $sections;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->sections = new ArrayCollection();
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
     * @return Collection<int, BlgArticle>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(BlgArticle $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->addTag($this);
        }

        return $this;
    }

    public function removeArticle(BlgArticle $article): static
    {
        if ($this->articles->removeElement($article)) {
            $article->removeTag($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgPage>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(BlgPage $page): static
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
            $page->addTag($this);
        }

        return $this;
    }

    public function removePage(BlgPage $page): static
    {
        if ($this->pages->removeElement($page)) {
            $page->removeTag($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(BlgCategory $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->addTag($this);
        }

        return $this;
    }

    public function removeCategory(BlgCategory $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeTag($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgSection>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(BlgSection $section): static
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->addTag($this);
        }

        return $this;
    }

    public function removeSection(BlgSection $section): static
    {
        if ($this->sections->removeElement($section)) {
            $section->removeTag($this);
        }

        return $this;
    }
}
