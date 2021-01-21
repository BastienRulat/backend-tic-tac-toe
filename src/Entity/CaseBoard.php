<?php

namespace App\Entity;

class CaseBoard {

    private function __construct (
        public int $row,
        public int $col,
        public int $value
    ) {}

    static function create (array $coordinates, int $value = 0)
    {
        list( $row, $col ) = $coordinates;
        return new CaseBoard ( $row, $col, $value );
    }

    public function __toString()
    {
        return match ($this->value) {
            0 => Tictactoe::PATTERN_PLAYERS[0],
            1 => Tictactoe::PATTERN_PLAYERS[1],
            2 => Tictactoe::PATTERN_PLAYERS[2]
        };
    }
}