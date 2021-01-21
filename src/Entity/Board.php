<?php

namespace App\Entity;

use App\Entity\CaseBoard;
use ValueError;

class Board {

    public array $state;

    public function __construct(array $newStateBoard) {
        
        foreach ($newStateBoard as $i => $row)
            foreach ($row as $case)
                $this->state[$i][] = $case;
    }
    
    public function setState(CaseBoard $c)
    {
        $this->state[$c->row][$c->col] = $c;
    }
}