<?php

namespace App\Entity;

use SplFixedArray;

class Board {

    private SplFixedArray $state;
    private string $sstate;
    private $size;

    public function __construct(int $boardSize = 3) {
        $this->state = new SplFixedArray($boardSize);
        for ($i=0; $i<$boardSize; $i++) {
            $this->state[$i] = new SplFixedArray($boardSize);
        }
        $this->sstate = str_pad('', $boardSize, '.', STR_PAD_RIGHT);
        $this->size = $boardSize;
    }

    public function getState() {

        return $this->state;
    }

    public function getSState() {
        return $this->sstate;
    }

    public function setSState ($state) {
        
        $this->sstate = $state;
    }

    public function getSize () {
        
        return $this->size;
    }
}