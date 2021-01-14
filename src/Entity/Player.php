<?php

namespace App\Entity;

class Player {

    private int $id;
    public string $name;
    public string $motif;

    public function __construct ($id, $name, $motif)
    {
        $this->id = $id;
        $this->name = $name;
        $this->motif = $motif;
    }
}