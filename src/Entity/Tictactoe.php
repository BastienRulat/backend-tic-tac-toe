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

          // Algorithme bidon
        // Parcours une fois la board en entier
        // Transforme les caractères en nombres pour faciliter les tests
        // Les flagO* permettent notamment de signaler dans le trait (ligne, col, ou diago)
        // Si oui, on sait que si sumO* est égal à boardSize ne signifie pas que X à gagner
        //     mais que la somme des X et des O est égal par coincidence à boardsize
        // Une fois cette précaution prise,
        //     Si sum* = boardsize etant donné que X = 1, alors le trait est plein de X
        //     Si sum* = boardsize * 2 étant donné que O = 2, alors le trait est plein de O 
        // Si c'est 2 conditions ne sont pas présentes, alors on regarde si on à croiser un '.'
        //     grâce au flagInprogress placé éventuellement à TRUE,
        //     SI oui, on retourne donc "InProgress"
        //     Sinon, pas de gagnant et c'est "Tie" !

        // Compteur de combinaisons victorieuses
        $Xwin = 0; //TODO
        $Owin = 0; //TODO
        // Flag de gestion des cas de retour
        $flagInProgress = false;
        $flagOrow = false;
        $flagOcol = array_fill (0, $boardSize, false);
        $flagOGD = false;
        $flagODG = false;
        // Variables de vérification pour trouver un gagnant
        $sumdiaGD = 0;
        $sumdiaDG = 0;
        $sumcols = array_fill (0, $boardSize, 0);
        $sumrow = 0;

        for ($j=0; $j<$boardSize; $j++) {
            
            for ($i=0; $i<$boardSize; $i++) {
    
                // Mise en forme de la donnée en nombre
                $case = match ($board[$j][$i]) {
                    '.' => 0,
                    'X' => 1,
                    'O' => 2
                };
               
                // Levez les flags !
                switch ($case) {
                    case 0: $flagInProgress = true; break;
                    case 2: $flagOrow       = true;
                            $flagOcol[$i]   = true; break;
                }

                // Ligne et colonne
                $sumcols[$i] += $case;
                $sumrow      += $case;
               
                // Si je suis sur la diagonale gauche droite
                if ( $i == $j ) { 
                    $sumdiaGD += $case;
                    if ($case == 2) $flagOGD=true;
                }
                // Si je suis sur la diagonale droite gauche
                if ( $i == $boardSize -1 - $j ) {
                    $sumdiaDG += $case;
                    if ($case == 2) $flagODG=true;
                }
            }
            
            // On vérifie les gagnants sur les lignes
            if ($sumrow == $boardSize && ! $flagOrow) return "X";
            if ($sumrow == $boardSize * 2) return "O";
            
            $sumrow = 0;
            $flagOrow = false;
        }

        // On vérifie les gagnants sur les diagonales
        if ($sumdiaGD == $boardSize && ! $flagOGD) return 'X';
        if ($sumdiaDG == $boardSize && ! $flagODG) return "X";
        if ($sumdiaGD == $boardSize * 2) return 'O';
        if ($sumdiaDG == $boardSize * 2) return "O";

        // On vérifie les gagnants sur les colonnes
        for ($i=0; $i<$boardSize; $i++) {
            if ($sumcols[$i] == $boardSize &&  ! $flagOcol[$i]) return "X";
            if ($sumcols[$i] == $boardSize * 2) return "O";
        }
 
        // Si pas de gagnants et qu'il reste des coups à jouer
        if ($flagInProgress) return "InProgress";

        // Si pas de gagnant et que la board est pleine
        return "Tie";
    }
}