<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait UrlTrait
{
    #[ORM\Column(length: 254)]
    private ?string $urlFR = null;

    #[ORM\Column(length: 254)]
    private ?string $urlEN = null;

    public function getUrlFR(): ?string
    {
        return $this->urlFR;
    }

    public function setUrlFR(?string $urlFR): self
    {
        $this->urlFR = $urlFR;
        return $this;
    }

    public function getUrlEN(): ?string
    {
        return $this->urlEN;
    }

    public function setUrlEN(?string $urlEN): self
    {
        $this->urlEN = $urlEN;
        return $this;
    }
}