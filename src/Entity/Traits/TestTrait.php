<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TestTrait
{
    #[ORM\Column]
    private bool $isDom = false;

    #[ORM\Column]
    private bool $isSub = false;

    #[ORM\Column]
    private bool $isGen = false;

    #[ORM\Column]
    private bool $isAdvanced = false;

    public function isIsDom(): bool
    {
        return $this->isDom;
    }

    public function setIsDom(bool $isDom): self
    {
        $this->isDom = $isDom;
        return $this;
    }

    public function isIsSub(): bool
    {
        return $this->isSub;
    }

    public function setIsSub(bool $isSub): self
    {
        $this->isSub = $isSub;
        return $this;
    }

    public function isIsGen(): bool
    {
        return $this->isGen;
    }

    public function setIsGen(bool $isGen): self
    {
        $this->isGen = $isGen;
        return $this;
    }

    public function isIsAdvanced(): bool
    {
        return $this->isAdvanced;
    }

    public function setIsAdvanced(bool $isAdvanced): self
    {
        $this->isAdvanced = $isAdvanced;
        return $this;
    }
}
