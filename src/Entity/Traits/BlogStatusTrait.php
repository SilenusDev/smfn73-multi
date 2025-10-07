<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait BlogStatusTrait
{
    #[ORM\Column]
    private bool $isPublished = false;

    #[ORM\Column]
    private bool $isFeatured = false;

    #[ORM\Column]
    private bool $isPinned = false;

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    public function isFeatured(): bool
    {
        return $this->isFeatured;
    }

    public function setIsFeatured(bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;
        return $this;
    }

    public function isPinned(): bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(bool $isPinned): self
    {
        $this->isPinned = $isPinned;
        return $this;
    }
}
