<?php

namespace App\Entity;

use App\Entity\Traits\TimeTrait;
use App\Repository\BlgLikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlgLikeRepository::class)]
class BlgLike
{
    use TimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'blgLikes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?BlgArticle $article = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?BlgPage $page = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: true)]
    private ?BlgComment $comment = null;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getComment(): ?BlgComment
    {
        return $this->comment;
    }

    public function setComment(?BlgComment $comment): static
    {
        $this->comment = $comment;

        return $this;
    }
}
