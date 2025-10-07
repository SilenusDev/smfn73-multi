<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait VisitTrait
{
    #[ORM\Column]
    private int $count = 0;
    
    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;  
        return $this;
    }
}
