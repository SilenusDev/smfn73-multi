<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait SubTrait
{
    #[ORM\Column(length: 254)]
    private ?string $subFR = null;

    #[ORM\Column(length: 254, nullable: true)]
    private ?string $subEN = null;

    public function getSubFR(): ?string
    {
        return $this->subFR;
    }

    public function setSubFR(?string $subFR): self
    {
        $this->subFR = $subFR;
        return $this;
    }

    public function getSubEN(): ?string
    {
        return $this->subEN;
    }

    public function setSubEN(?string $subEN): self
    {
        $this->subEN = $subEN;
        return $this;
    }
}