<?php

namespace App\Entity;

use App\Entity\CaseBoard;

class Player {

    public function __construct
    (
        public string $id,
        public string $name,
        public string $motif,
    ) {}

    public function getId ()
    {
        return $this->id;
    }
    
    public function play (CaseBoard $c)
    {
        $this->tictactoe->play($this->id, $c);
    }
}