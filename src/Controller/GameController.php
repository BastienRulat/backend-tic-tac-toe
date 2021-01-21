<?php

namespace App\Controller;

use Error;
use ValueError;
use SplFileObject;
use App\Entity\Tictactoe;
use App\Entity\Coordinates;
use App\Entity\CaseBoard;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;

class GameController {
    
    public static bool $INIT = false;
    public static bool $NEW_TICTACTOE_GAME = false;

    public static $routes = 0;
    public Tictactoe $tictactoe;
        
    /**
     * Initialisation du GameController, le pilote du TicTacToe.
     * @return GameController 
     */
    public static function create () :GameController {
        
        $GameController = new GameController;
        GameController::$INIT = true;

        return $GameController;

        // Without Services Container, Routing Isolate System, Any Configurations and Events Sourcing
        // For the moment 😋 
    }

    /** 
     *  NEXT STEP TODO : Mini Router
     *  Piloter le paramétrage la configuration des routes d'ici.
     *  Mais en externalisant la logique dans un Service 
     * @param Request $Request 
     * @return void 
     * @throws SuspiciousOperationException 
     */
    public function routing (Request $Request) {     

        /**
         * @Route [ name='newTictactoe' "slug='start-new-tictactoe-game'" controller='startNewTictatoeGame',
         *          path='/', method='GET', param = [s={\d}] ] 
         */
        if ( $Request->isMethod( 'GET' ))
        {
            if ($size = $Request->query->get( 's' ))
            {
                $this->createNewTictactoe( (int)$size );
                
                $body  = sprintf( "<h1>Tictactoe ($size)</h1>" );
                $body .= $this->displayTictactoe();
                $this->render( $body );
            }
            else $this->home();
        }
        
        /**
         * @Route [ name='playing'], "slug='playing-tictactoe'" controller='playingTictatoeGame',
         *          path='/' method='POST'(XHR), param = [$row{\d}&col\d]
         *  NEXT STEP TODO : Authentication Token pour le joueur
         */
        elseif ( $Request->isMethod('POST')
        &&   $Request->request->get( 'row' ) !== null ?: throw new SuspiciousOperationException("Données corrompues") 
        &&   $Request->request->get( 'col' ) !== null ?: throw new SuspiciousOperationException("Données corrompues") )
        {    
                $this->run( CaseBoard::create([
                    (int)$Request->request->get( 'row' ),
                    (int)$Request->request->get( 'col' )
                ]));
        }
        
        /**
         * @Function URLMacther à implémenter ici dans un premier temps
         * Parse les URL et appelle la Callable Closure Route prévue définie ci-dessus, i.E une méthode de controlleur.
         * On sort les traitements de parsing, on s'appuie sur des Validators Tools OO.
         */

    }
    
    /**
     * Fonction orchestrateur métier de ma route playing
     * TODO Redécoupler le sparties vues et modèles, c'est le foutoir ici 😅.
     * @param Coordinates $coordinates 
     * @return exit 
     * @throws ValueError 
     * @throws Error 
     */
    public function run (CaseBoard $caseBoard) {

        $tictactoe = $this->loadTictactoe();
        
        $caseBoard->value = $tictactoe->currentPlayer;
        
        $tictactoe->nextState($caseBoard);
       
        $this->saveTictactoe();
       
        $response["motif"] = $tictactoe->players[$caseBoard->value]->motif;
        $response["currentPlayer"] = $tictactoe->players[$tictactoe->currentPlayer]->name;
       
        $response ["winner"] =  $tictactoe->thereIsWinnerVraiCStyle() ?: false;
       
        
        echo json_encode($response);
    }
    

    /**
     * Crée Tictactoe Entity
     * Nous sommes sur le 'C'rud
     * Doit relever d'une Data Classe de Tictactoe et non du GameController
     * TODO TICTACTOE MODEL
     * TODO AJOUTER TYPE_IN_MEMORY_BOARD : CSTYLE, ARRAYINT, ARRAYCASE, DS
     * @param mixed $size 
     * @return Tictactoe 
     * @throws ValueError 
     */
    public function createNewTictactoe (array|string|int $in = 3) :Tictactoe { 

        $tictactoe = $this->tictactoe = Tictactoe::create($in);
        
        GameController::$NEW_TICTACTOE_GAME = true;
        
        $this->saveTictactoe();

        return $tictactoe;
    }

    /**
     * Récupère les données sauvegardées et les charges en mémoire vive
     * Tictactoe Repository
     * Doit relever d'une Data Classe de Tictactoe et non du GameController
     * Nous sommes sur le c'R'ud
     * TODO TICTACTOE MODEL
     * @return Tictactoe 
     * @throws ValueError 
     */
    public function loadTictactoe (string $filename = "data.csv") :Tictactoe {

        $csv = new SplFileObject( $filename, "r" );
        $csv->setFlags( SplFileObject::READ_CSV );
        
        Tictactoe::$SIZE = (int)$csv->fgets();
        $tictactoe = $this->tictactoe = Tictactoe::create( Tictactoe::$SIZE );
        $tictactoe->currentPlayer = (int)$csv->fgets();
        $state = $tictactoe->board->state;
        
        foreach( $state as $row => $r)
        {
            
            // die(var_dump($csv->fgetcsv()));
            foreach ( $csv->fgetcsv() as $col => $case ) 
            {
                $case = intval($case);
                $tictactoe->board->state [$row][$col] = CaseBoard::create( [$row, $col],
                    $case == Tictactoe::NO_PLAYER_ID || $case == Tictactoe::PATTERN_PLAYERS[Tictactoe::NO_PLAYER_ID] ? 0
                  : Tictactoe::normalizeIdPlayer($case)
                );
            }
        }
        // die(var_dump($tictactoe->board->state));
        
            // echo "<pre>"; var_dump($this->tictactoe->board->state); echo("</pre>");
        return $this->tictactoe;
    }

    /**
     * Fait persister les données en mémoire.
     * Nous sommes sur le cr'U'd
     * @return void 
     */
    public function saveTictactoe (string $file = "data.csv") {

        $file = new SplFileObject( $file, 'w+' );
        $file->fputcsv( [Tictactoe::$SIZE] );
        $file->fputcsv( [$this->tictactoe->currentPlayer] );
        $state = $this->tictactoe->board->state;
        // var_dump($state);
        
        foreach ( $state as $row ) {     
            $str = [];
            foreach ($row as $c) {
                $str [] = $c->value;
            }
            $file->fputcsv($str);
        }
        
        // foreach ($str as $s) 
        // die("eeeAAAAeetreter");
    }

    /**
     * Le cru'D' ?
     * @return void 
     */
    public function terminateTictactoe () {

    }

    /**
     * Template View Tictactoe à passer au render
     * @return void 
     */
    public function home () {

       $body = "
            <h1>TictacToe - New Game</h1>
            <form action='/' type='get'>
                <div>
                    <label for='s'>Entrez la taille de la grille souhaitée</label>
                    <input type='number' name='s' value='3'>
                </div>
                <div>
                    <input type='submit' value='Go !'>
                </div>
            </form>
       ";

       $this->render($body);
    }

    /**
     * C'est tout View.
     * @return string 
     */
    public function displayTictactoe () {
        
        $board = $this->tictactoe->board;
        $viewTictactoe = "<div><table id='viewTictactoe'>";
        
        foreach (range(0, Tictactoe::$SIZE-1) as $row) {
            $viewTictactoe .= "<tr>";
            foreach (range(0,Tictactoe::$SIZE-1) as $col) {
                $viewTictactoe .= "<td data-row='$row' data-col='$col' onclick='sendCase(this)'>" . $this->tictactoe->createCaseBoard([$row, $col]) ."</td>";
            }
            $viewTictactoe .= "</tr>";
        }
        $viewTictactoe .= "</table></div>";
        $viewTictactoe .= "<h3>Current player : <span id='currentPlayer'/></h3>";
        $viewTictactoe .= "<form action='/' method_'GET'><input type='submit' value='Retourner au menu'></form>";

        return $viewTictactoe;
    }

    /**
     * La baguette magique du GameController
     * Qui pue un peu pour l'instant 🙄
     * @param mixed $body 
     * @return void 
     */
    public function render ($body) {

        echo "
            <html>
            <head>
            <title>Tictactoe</title>
            <link rel='stylesheet' type='text/css' href='index.css'/>
            </head>
            <body>
            "
            . $body .
            "
            <script type='text/javascript' src='tictactoe.js'></script>
            </body>
            </html>
            ";
    }
}