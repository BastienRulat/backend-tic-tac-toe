<?php

namespace App\Entity;

use App\Entity\Board;
use App\Entity\Player;

class Tictactoe {

    private Board $board;
    private array $players;

    /**
     * 
     * @var App\Controller\Tictactoe\__construct
     */
    private function __construct (int $boardSize) {
        
        $this->board = new Board($boardSize);
        
        $this->players[1] = new Player (1, "Player 1", "X");
        $this->players[2] = new Player (2, "Player 2", "O");
    }

    /**
     * 
     * @return Tictactoe|null
     */
    public static function create ($boardSize = 3) : Tictactoe|null {
        
        if (! @is_int($boardSize) || $boardSize < 3) return null;
        
        return new Tictactoe($boardSize);
    }

    public function getBoard () : Board {
        return $this->board;
    }

    public function getPlayers () :array {
        return $this->players;
    }

    public function getPlayer1 () :array {
        return $this->players[1];
    }

    public function getPlayer2 () :array {
        return $this->players[2];
    }

    public function andTheWinnerIs (array|string $state) {

        $boardSize = $this->board->getSize();

        if (is_string($state)) {
            $board = str_split($state, $boardSize);
        }
        else {
            $board = $state;
        }

        $Xwin = 0;
        $Owin = 0;
        $flagInProgress = false;
        $flagOrow = false;
        $flagOcol = array_fill (0, $boardSize, false);
        $flagOGD = false;
        $flagODG = false;
        $sumdiaGD = 0;
        $sumdiaDG = 0;

        $sumcols = array_fill (0, $boardSize, 0);
        $sumrow = 0;
        for ($j=0; $j<$boardSize; $j++) {
            $board[$j] = str_replace([".", "X", "O"], ["0","1","2"], $board[$j]);
            
            for ($i=0; $i<$boardSize; $i++) {
               
                $case = $board[$j][$i];
               
                if ( $case == 0) $flagInProgress = true;
                else if ( $case == 2) {
                    $flagOrow = true;
                    $flagOcol[$i] = true;
                }

                $sumcols[$i] += $case;
                $sumrow += $case;
               
                if ( $i == $j ) { 
                    $sumdiaGD += $board[$j][$i];
                    if ($case == 2) $flagOGD=true;
                }
                if ( $i == $boardSize -1 - $j ) {
                    $sumdiaDG += $board[$j][$i];
                    if ($case == 2) $flagODG=true;
                }
            }

            if ($sumrow == $boardSize && ! $flagOrow) return "X";
            if ($sumrow == $boardSize * 2) return "O";
            $sumrow = 0;
            $flagOrow = false;
        }

        if ($sumdiaGD == $boardSize && ! $flagOGD) return 'X';
        if ($sumdiaDG == $boardSize && ! $flagODG) return "X";
        if ($sumdiaGD == $boardSize * 2) return 'O';
        if ($sumdiaDG == $boardSize *2) return "O";

        for ($i=0; $i<$boardSize; $i++) {
            if ($sumcols[$i] == $boardSize &&  ! $flagOcol[$i]) return "X";
            if ($sumcols[$i] == $boardSize * 2) return "O";
        }
// 
        if ($flagInProgress) return "InProgress";
        return "Tie";
    }
}