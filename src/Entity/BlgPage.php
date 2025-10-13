<?php

namespace App\Entity;

use App\Entity\Traits\BlogStatusTrait;
use App\Entity\Traits\ContentTrait;
use App\Entity\Traits\SeoTrait;
use App\Entity\Traits\TimeTrait;
use App\Entity\Traits\TitleTrait;
use App\Repository\BlgPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlgPageRepository::class)]
class BlgPage
{
    use TimeTrait;
    use TitleTrait;
    use ContentTrait;
    use SeoTrait;
    use BlogStatusTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blgPages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?BlgCategory $category = null;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?BlgSection $section = null;

    /**
     * @var Collection<int, BlgTag>
     */
    #[ORM\ManyToMany(targetEntity: BlgTag::class, inversedBy: 'pages')]
    private Collection $tags;

    /**
     * @var Collection<int, BlgComment>
     */
    #[ORM\OneToMany(targetEntity: BlgComment::class, mappedBy: 'page', orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, BlgLike>
     */
    #[ORM\OneToMany(targetEntity: BlgLike::class, mappedBy: 'page', orphanRemoval: true)]
    private Collection $likes;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCategory(): ?BlgCategory
    {
        return $this->category;
    }

    public function setCategory(?BlgCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getSection(): ?BlgSection
    {
        return $this->section;
    }

    public function setSection(?BlgSection $section): static
    {
        $this->section = $section;

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

    /**
     * @return Collection<int, BlgComment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(BlgComment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPage($this);
        }

        return $this;
    }

    public function removeComment(BlgComment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            if ($comment->getPage() === $this) {
                $comment->setPage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BlgLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(BlgLike $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setPage($this);
        }

        return $this;
    }

    public function removeLike(BlgLike $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getPage() === $this) {
                $like->setPage(null);
            }
        }

        return $this;
    }
}
