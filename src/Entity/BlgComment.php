<?php

namespace App\Entity;

use App\Entity\Traits\ContentTrait;
use App\Entity\Traits\ParentTrait;
use App\Entity\Traits\TimeTrait;
use App\Repository\BlgCommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlgCommentRepository::class)]
class BlgComment
{
    use TimeTrait;
    use ContentTrait;
    use ParentTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blgComments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?BlgArticle $article = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true)]
    private ?BlgPage $page = null;

    /**
     * @var Collection<int, BlgLike>
     */
    #[ORM\OneToMany(targetEntity: BlgLike::class, mappedBy: 'comment', orphanRemoval: true)]
    private Collection $likes;

    public function __construct()
    {
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

    public function getArticle(): ?BlgArticle
    {
        return $this->article;
    }

    public function setArticle(?BlgArticle $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getPage(): ?BlgPage
    {
        return $this->page;
    }

    public function setPage(?BlgPage $page): static
    {
        $this->page = $page;

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
            $like->setComment($this);
        }

        return $this;
    }

    public function removeLike(BlgLike $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getComment() === $this) {
                $like->setComment(null);
            }
        }

        return $this;
    }
}
