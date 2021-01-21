<?php

namespace App\Entity;

use dump;
use Error;
use App\Entity\Board;
use App\Entity\Player;
use UnexpectedValueException;
use App\Entity\CaseBoard;
use TypeError;

class Tictactoe {

    const  MIN_SIZE = 3;
    const  MAX_SIZE = 20;
    const  DEFAULT_SIZE = 3;
    const  DIMENSION = 2;
    const  NO_PLAYER_ID = 0;
    const  NB_PLAYERS = 2;
    const  PATTERN_PLAYERS = ['.', 'X', 'O'];
    const  FIRST_COORDINATE_VALUE_CASEBOARD = 0;
    static   int  $SIZE = self::DEFAULT_SIZE;
    
    static string $ERROR_MESSAGE_SIZE              = "La taille du TictacToe doit être comprise entre self::MIN_SIZE et self::MAX_SIZE";
    static string $ERROR_MESSAGE_INPUT_TYPE_STATE  = "On ne peut spécifier un état du Tictactoe qu'à l'aide d'une string ou d'un array";
    static string $ERROR_MESSAGE_TYPE_CASE         = "Les coordonnées d'une case de Board sont comprises entre 0 et self::MAX_SIZE-1";
    static string $ERROR_MESSAGE_DIMENSION         = "2 coordonnées sont nécessaires pour créer une case, 2 coordonnées ont été renseignées.";
    static string $MESSAGE_ERROR_PLAYER_VALUE      = "Un joueur est représenté par son motif ou son id. Par défaut, 2 joueurs et une valeur case vide ['.', 'X', 'O'] => [0, 1, 2] input a été reçu.";
    
    
    public int $currentPlayer = 0;

    /**
     * @var App\Controller\Tictactoe\__construct
     */
    private function __construct ( public Board $board, public array $players)  {
        
        $this->player1 = $players[1];
        $this->player2 = $players[2];

        $this->hasCurrentPlayer() ?: $this->findAndSetCurrentPlayer( $board );
    }

    /**
     * Tictactoe Simple Factory
     * @return Tictactoe|null
     */
    public static function create ( array|string|int $in = self::DEFAULT_SIZE ) :Tictactoe|null {
        
        // Compositions
        $players = self::createPlayers();
        $board   = self::createBoard( $in );
       
        $tictactoe =  new Tictactoe( $board, $players );

        return $tictactoe;   
    }

    public static function createPlayers () {

        $players[1] = new Player (1, "Player 1", "X" );
        $players[2] = new Player (2, "Player 2", "O" );

        return $players;
    }

    public static function normalizeTypeIdPlayer (string|int $id)
    {
        return (int)$id;
    }
    
    public static function normalizeIdPlayer (string|int $id) :int
    {
        $id = self::normalizeTypeIdPlayer($id);

        return self::is_idPlayer ($id) ? $id
             : throw new UnexpectedValueException(self::$MESSAGE_ERROR_PLAYER_VALUE);
    }

    public static function is_idPlayer (int $id) :bool
    {
        return  $id >= 1 && $id <= self::NB_PLAYERS;
    }

    public function hasCurrentPlayer () :bool
    {
        return $this->currentPlayer == 0 ? false : true;
    }

    public function findAndSetCurrentPlayer (Board $board) :void
    {
        $res = 0;
        foreach ( $board->state as $row ) {
            foreach ( $row as $case ) {
                $res += match ( $case->value ) {
                    0 =>  0,
                    1 =>  1,
                    2 => -1
                };
            }
        }
        $this->currentPlayer = $res > 0 ? 2 : 1;
    }

    private function changeCurrentPlayer ()
    {
        $this->currentPlayer = match ($this->currentPlayer) {
            1 => 2,
            2 => 1
        };
    }

    public static function createBoard ( int|string|array $in ) {

        return new Board ( self::normalizeStateBoard( $in )) ??
         throw new Error ( "Impossible de créer la nouvelle Board" );
    }

    public static function normalizeSize (array|string|int $in)
    {
        $size =  self::normalizeTypeSize($in);
        return   self::is_validSize($size) ? $size
               : throw new UnexpectedValueException( self::$ERROR_MESSAGE_SIZE);
    }

    public static function normalizeTypeSize (array|string|int $in) :int {

        return    is_int    ($in) ? $in 
              : ( is_string ($in) ? (int)sqrt(strlen($in))
              : ( is_array  ($in) ? count($in)
              : throw new TypeError ( self::$ERROR_MESSAGE_INPUT_TYPE_STATE )));
    }

    public static function is_validSize (int $size) :bool {

        return $size >= self::MIN_SIZE && $size <= self::MAX_SIZE;
    }

    public static function transformStringToStringStateBoard (string $serialized) :array
    {
        $array = str_split ($serialized, self::$SIZE);
        foreach ( range(0, self::$SIZE-1) as $i )
            $array[$i] = str_split ($array[$i]); 
        
        return $array;
    }

    public static function transformStringStateBoardToStateBoard (array $in) :array
    {
        foreach ( $in as $row => $r ) {
            foreach ($r as $col => $inputValueCase) {
                $newStateBoard[$row][$col] = match ($inputValueCase) {
                    "X", "1"  => self::createCaseBoard([$row, $col], 1),
                    "O", "2"  => self::createCaseBoard([$row, $col], 2),
                    default   => self::createCaseBoard([$row, $col])
                };
            }
        }
        return $newStateBoard;
    }

    public static function createVirginStateBoard (int $size)
    {
        foreach ( range(0, $size - 1) as $row)
            foreach ( range(0, $size - 1) as $col)
                $newStateBoard [$row][$col]= self::createCaseBoard([$row, $col]);
        return $newStateBoard;
    }

    public static function normalizeStateBoard (array|string|int $in ) :array {

        self::$SIZE = self::normalizeSize( $in );

        if     (is_int   ($in)) return self::createVirginStateBoard($in);
        elseif (is_string($in)) $in =  self::transformStringToStringStateBoard($in);
        
        return self::transformStringStateBoardToStateBoard($in);   
    }

    public static function createCaseBoard (array $coordinates, int $idPlayer = 0) {
        
          count($coordinates) === self::DIMENSION ?
        : throw new UnexpectedValueException(self::$ERROR_MESSAGE_DIMENSION);

        foreach ( $coordinates as $c )
              is_int($c) && $c >= 0 && $c <= self::$SIZE-1 ?
            : throw new UnexpectedValueException(self::$ERROR_MESSAGE_TYPE_CASE);
        
        return CaseBoard::create ($coordinates, $idPlayer);
    }

    public function nextState (CaseBoard $c)
    {
        $this->updateCaseBoard($c);
    }

    private function updateCaseBoard (Caseboard $c)
    {
        $board = $this->board;
      
        switch ($board->state[$c->row][$c->col]->value) {
            case 0 : $board->setState($c);
                     $this->changeCurrentPlayer();
                     break;
            case 1 : break;
            case 2 : break;
        }
    }

    /**
     * Algorithme de vérification de grille Tictactoe version 2
     * Vous avez cru que mon premier jet était du C ?
     * Et bien alors voici de l'assembleur 🤣
     * J'ai mis des foreach php vous avez vu ? 😂
     * Et j'ai viré le SplFixedArray même si pour les perfs pour des Tictactoe N*N*N...
     * On doir perdre un peu !
     * Et j'ai levé les Flags flags pour être sympas, sauf pour Tie 🏳‍🌈
     * A la place j'ai fait des "mixed value" mi-booléenne mi-séquentielle
     * Je pense que c'est pas mal efficace déjà
     * En tout cas c'est en dessous de O(n²) c'est un bon début 🧐
     * @return array|false 
     */
    public function thereIsWinnerVraiCStyle () :array|string|false
    {
        $sizeBoard = Tictactoe::$SIZE;
        $stateBoard = $this->board->state;

        $tie = true;
        $rows = 0;
        $diaGD = $stateBoard[0][0]->value;
        $diaDG = $stateBoard[$sizeBoard-1][0]->value;
        
        foreach (range(0, $sizeBoard-1) as $i)
            $cols[$i] = $stateBoard[$sizeBoard-1][$i]->value;
        
        foreach (range(0,$sizeBoard-1) as $row) {
            if ($rows) return [$rows, "row", $row];
            $rows = $stateBoard[$row][0]->value;
            foreach (range(0,$sizeBoard-1) as $col) {
                
                $case = $stateBoard[$row][$col]->value;

                $cols[$col] = $cols[$col] == $case ? $cols[$col] : 0;

                if ( $rows != $case || ! $case) {
                    $diaGD = $diaGD == $stateBoard[$row][$row]->value ? $diaGD : 0;
                    $diaDG = $diaDG == $stateBoard[$row][$sizeBoard-$row-1]->value ? $diaDG : 0;
                    $rows = 0;
                    if ($case == 0) $tie = false;
                }
                if ($row == $sizeBoard -1 && $cols[$col]) return [$cols[$col], "col", $col];
            }
        }
        if ($rows)  return [$rows, "row", $row];
        if ($diaGD) return [$diaGD,'dia','GD'];
        if ($diaDG) return [$diaDG,'dia','DG'];

        if ($tie) return "Tie";
        return false;
    }

    /** Le premier Algorithme bidon
    * Parcours une fois la board en entier
    * Transforme les caractères en nombres pour faciliter les tests
    * Les flagO* permettent notamment de signaler dans le trait (ligne, col, ou diago)
    * Si oui, on sait que si sumO* est égal à sizeBoard ne signifie pas que X à gagner
    *     mais que la somme des X et des O est égal par coincidence à sizeBoard
    * Une fois cette précaution prise,
    *     Si sum* = sizeBoard etant donné que X = 1, alors le trait est plein de X
    *     Si sum* = sizeBoard * 2 étant donné que O = 2, alors le trait est plein de O 
    * Si c'est 2 conditions ne sont pas présentes, alors on regarde si on à croiser un '.'
    *     grâce au flagInprogress placé éventuellement à TRUE,
    *     SI oui, on retourne donc "InProgress"
    *     Sinon, pas de gagnant et c'est "Tie" !
    */
    public function andTheWinnerIs (array|string $state)
    {
        $sizeBoard = Tictactoe::$SIZE;
        $board = is_string($state) ? str_split($state, $sizeBoard) : $state;

        // Flag de gestion des cas de retour
        $flagInProgress = false;
        $flagOrow = false;
        $flagOcol = array_fill (0, $sizeBoard, false);
        $flagOGD = false;
        $flagODG = false;
        // Variables de vérification pour trouver un gagnant
        $sumdiaGD = 0;
        $sumdiaDG = 0;
        $sumcols = array_fill (0, $sizeBoard, 0);
        $sumrow = 0;

        foreach (range(0,$sizeBoard-1) as $row) {
            foreach (range(0,$sizeBoard-1) as $col) {
    
                $case = $board[$row][$col];
                // Mise en forme de la donnée en nombre
                $case = match ($board[$row][$col]) {
                    '.' => 0,
                    'X' => 1,
                    'O' => 2
                };
               
                // Levez les flags !
                switch ($case) {
                    case 0: $flagInProgress = true; break;
                    case 2: $flagOrow       = true;
                            $flagOcol[$col] = true; break;
                }

                // Ligne et colonne
                $sumcols[$col] += $case;
                $sumrow        += $case;
               
                // Si je suis sur la diagonale gauche droite
                if ( $col == $row ) { 
                    $sumdiaGD += $case;
                    if ($case == 2) $flagOGD=true;
                }
                // Si je suis sur la diagonale droite gauche
                if ( $col == $sizeBoard -1 -$row ) {
                    $sumdiaDG += $case;
                    if ($case == 2) $flagODG=true;
                }
            }
            
            // On vérifie les gagnants sur les lignes
            if ($sumrow == $sizeBoard && ! $flagOrow) return "X";
            if ($sumrow == $sizeBoard * 2) return "O";
            
            $sumrow = 0;
            $flagOrow = false;
        }

        // On vérifie les gagnants sur les diagonales
        if ($sumdiaGD == $sizeBoard && ! $flagOGD) return 'X';
        if ($sumdiaDG == $sizeBoard && ! $flagODG) return 'X';
        if ($sumdiaGD == $sizeBoard * 2) return 'O';
        if ($sumdiaDG == $sizeBoard * 2) return 'O';

        // On vérifie les gagnants sur les colonnes
        for ($col=0; $col<$sizeBoard; $col++) {
            if ($sumcols[$col] == $sizeBoard &&  ! $flagOcol[$col]) return 'X';
            if ($sumcols[$col] == $sizeBoard * 2) return 'O';
        }
 
        return $flagInProgress ? "InProgress" : "Tie";
    }
}