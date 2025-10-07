<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait SeoTrait
{
   
    #[ORM\Column(length: 254)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'Le slug doit contenir uniquement des lettres minuscules, chiffres et tirets')]
    private ?string $slugFR = null;

    #[ORM\Column(length: 254, nullable: true)]
    #[Assert\Regex(pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'Le slug doit contenir uniquement des lettres minuscules, chiffres et tirets')]
    private ?string $slugEN = null;

    #[ORM\Column(length: 254)]
    private ?string $descrFR = null;

    #[ORM\Column(length: 254, nullable: true)]
    private ?string $descrEN = null;

    #[ORM\Column(length: 254, nullable: true)]
    private ?string $tagsFR = null;

    #[ORM\Column(length: 254, nullable: true)]
    private ?string $tagsEN = null;

    #[ORM\Column(length: 254, nullable: true)]
    private ?string $urlFR = null;

    #[ORM\Column(length: 254, nullable: true)]
    private ?string $urlEN = null;

    #[ORM\Column(length: 254, nullable: true)]
    private ?string $img = null;

    public function getSlugFR(): ?string
    {
        return $this->slugFR;
    }

    public function setSlugFR(?string $slugFR): self
    {
        $this->slugFR = $slugFR;
        return $this;
    }

    public function getSlugEN(): ?string
    {
        return $this->slugEN;
    }

    public function setSlugEN(?string $slugEN): self
    {
        $this->slugEN = $slugEN;
        return $this;
    }

    public function getDescrFR(): ?string
    {
        return $this->descrFR;
    }

    public function setDescrFR(?string $descrFR): self
    {
        $this->descrFR = $descrFR;
        return $this;
    }

    public function getDescrEN(): ?string
    {
        return $this->descrEN;
    }

    public function setDescrEN(?string $descrEN): self
    {
        $this->descrEN = $descrEN;
        return $this;
    }

    public function getTagsFR(): ?string
    {
        return $this->tagsFR;
    }

    public function setTagsFR(?string $tagsFR): self
    {
        $this->tagsFR = $tagsFR;
        return $this;
    }

    public function getTagsEN(): ?string
    {
        return $this->tagsEN;
    }

    public function setTagsEN(?string $tagsEN): self
    {
        $this->tagsEN = $tagsEN;
        return $this;
    }

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

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;
        return $this;
    }
}