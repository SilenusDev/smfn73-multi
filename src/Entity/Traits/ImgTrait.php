<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait ImgTrait
{
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $img = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $imgAlt = null;

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;
        return $this;
    }

    public function getImgAlt(): ?string
    {
        return $this->imgAlt;
    }

    public function setImgAlt(?string $imgAlt): self
    {
        $this->imgAlt = $imgAlt;
        return $this;
    }
}