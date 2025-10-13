<?php

namespace App\Entity\Traits;

use App\Enum\BlogStatusEnum;
use Doctrine\ORM\Mapping as ORM;

trait BlogStatusTrait
{
    #[ORM\Column(length: 20, enumType: BlogStatusEnum::class)]
    private ?BlogStatusEnum $status = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $viewCount = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $likeCount = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $commentCount = 0;

    public function getStatus(): ?BlogStatusEnum
    {
        return $this->status;
    }

    public function setStatus(BlogStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Vérifie si le contenu est publié
     */
    public function isPublished(): bool
    {
        return $this->status === BlogStatusEnum::PUBLISHED;
    }

    /**
     * Vérifie si le contenu est en brouillon
     */
    public function isDraft(): bool
    {
        return $this->status === BlogStatusEnum::DRAFT;
    }

    /**
     * Vérifie si le contenu est privé
     */
    public function isPrivate(): bool
    {
        return $this->status === BlogStatusEnum::PRIVATE;
    }

    /**
     * Vérifie si le contenu est premium
     */
    public function isPremium(): bool
    {
        return $this->status === BlogStatusEnum::PREMIUM;
    }

    /**
     * Retourne le label du statut
     */
    public function getStatusLabel(): ?string
    {
        return $this->status?->getLabel();
    }

    /**
     * Retourne la couleur du statut
     */
    public function getStatusColor(): ?string
    {
        return $this->status?->getColor();
    }

    /**
     * Retourne l'icône du statut
     */
    public function getStatusIcon(): ?string
    {
        return $this->status?->getIcon();
    }

    // ========== Statistiques ==========

    public function getViewCount(): int
    {
        return $this->viewCount;
    }

    public function setViewCount(int $viewCount): self
    {
        $this->viewCount = $viewCount;
        return $this;
    }

    /**
     * Incrémente le nombre de vues
     */
    public function incrementViewCount(): self
    {
        $this->viewCount++;
        return $this;
    }

    public function getLikeCount(): int
    {
        return $this->likeCount;
    }

    public function setLikeCount(int $likeCount): self
    {
        $this->likeCount = $likeCount;
        return $this;
    }

    /**
     * Incrémente le nombre de likes
     */
    public function incrementLikeCount(): self
    {
        $this->likeCount++;
        return $this;
    }

    /**
     * Décrémente le nombre de likes
     */
    public function decrementLikeCount(): self
    {
        if ($this->likeCount > 0) {
            $this->likeCount--;
        }
        return $this;
    }

    public function getCommentCount(): int
    {
        return $this->commentCount;
    }

    public function setCommentCount(int $commentCount): self
    {
        $this->commentCount = $commentCount;
        return $this;
    }

    /**
     * Incrémente le nombre de commentaires
     */
    public function incrementCommentCount(): self
    {
        $this->commentCount++;
        return $this;
    }

    /**
     * Décrémente le nombre de commentaires
     */
    public function decrementCommentCount(): self
    {
        if ($this->commentCount > 0) {
            $this->commentCount--;
        }
        return $this;
    }

    /**
     * Vérifie si le contenu est populaire (plus de 100 vues)
     */
    public function isPopular(): bool
    {
        return $this->viewCount >= 100;
    }

    /**
     * Vérifie si le contenu a des interactions
     */
    public function hasInteractions(): bool
    {
        return $this->likeCount > 0 || $this->commentCount > 0;
    }

    /**
     * Retourne le score d'engagement (likes + commentaires)
     */
    public function getEngagementScore(): int
    {
        return $this->likeCount + ($this->commentCount * 2); // Les commentaires valent 2x plus
    }
}
