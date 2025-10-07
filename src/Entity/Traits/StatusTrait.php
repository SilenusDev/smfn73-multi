<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait StatusTrait
{
    #[ORM\Column]
    private bool $isActive = false;

    #[ORM\Column]
    private bool $isPrivate = false;

    #[ORM\Column]
    private bool $isPrem = false;

    #[ORM\Column]
    private bool $isParent = false;

    #[ORM\Column]
    private bool $isAnswer = false;

    #[ORM\Column]
    private bool $isTreated = false;

    public function isIsActive(): bool
    {
        return $this->isActive;
    }
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isIsPrivate(): bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;
        return $this;
    }

    public function isIsPrem(): bool
    {
        return $this->isPrem;
    }

    public function setIsPrem(bool $isPrem): self
    {
        $this->isPrem = $isPrem;
        return $this;
    }

    public function isIsParent(): bool
    {
        return $this->isParent;
    }

    public function setIsParent(bool $isParent): self
    {
        $this->isParent = $isParent;
        return $this;
    }

    public function isIsAnswer(): bool
    {
        return $this->isAnswer;
    }

    public function setIsAnswer(bool $isAnswer): self
    {
        $this->isAnswer = $isAnswer;
        return $this;
    }

    public function isIsTreated(): bool
    {
        return $this->isTreated;
    }

    public function setIsTreated(bool $isTreated): self
    {
        $this->isTreated = $isTreated;
        return $this;
    }
    
}