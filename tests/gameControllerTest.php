<?php declare(strict_types=1);

namespace Test;

use PHPUnit\Framework\TestCase;
use App\Controller\GameController;
use App\Entity\Tictactoe;

class gameControllerTest extends TestCase {

    public function test_if_I_can_observe_a_correct_running_start_GameController () {
        
        $this->assertFalse( GameController::$INIT );
        $this->assertFalse( GameController::$NEW_TICTACTOE_GAME );

        $GameController =   GameController::create();
        $this->assertTrue ( GameController::$INIT );

        $GameController->createNewTictactoe(4);
        // $this->assertTrue ( GameController::$NEW_TICTACTOE_GAME );
    }

    public function test_if_I_can_load_a_file () {

        $GameController =   GameController::create();
        
        $GameController->loadTictactoe(__DIR__."/files/test_if_I_can_load_a_file.csv");

        $expectedStateBoard = Tictactoe::transformStringToStringStateBoard("X..OX....");
        $expectedStateBoard = Tictactoe::transformStringStateBoardToStateBoard($expectedStateBoard);

        $this->assertEquals($expectedStateBoard, $GameController->tictactoe->board->state);
    }

    public function test_if_I_can_save_a_new_game_in_a_file () {

        $GameController = GameController::create();
        $GameController->createNewTictactoe();
        $GameController->saveTictactoe();
        $GameController->loadTictactoe(__DIR__."/files/test_if_I_can_save_a_new_game_in_a_file.csv");

        $expectedStateBoard = Tictactoe::transformStringToStringStateBoard(".........");
        $expectedStateBoard = Tictactoe::transformStringStateBoardToStateBoard($expectedStateBoard);
        
        $this->assertEquals($expectedStateBoard, $GameController->tictactoe->board->state);
    }

    public function test_if_I_can_save_a_game_in_a_file () {

        $GameController = GameController::create();
        $GameController->createNewTictactoe();
        $GameController->tictactoe->board->setState(Tictactoe::createCaseBoard([0,0],1));
        $GameController->tictactoe->board->setState(Tictactoe::createCaseBoard([0,1],1));
        $GameController->tictactoe->board->setState(Tictactoe::createCaseBoard([2,0],1));
        $GameController->saveTictactoe(__DIR__."/files/test_if_I_can_save_a_game_in_a_file.csv");
        $GameController->loadTictactoe(__DIR__."/files/test_if_I_can_save_a_game_in_a_file.csv");

        $expectedStateBoard = Tictactoe::transformStringToStringStateBoard("XX....X..");
        $expectedStateBoard = Tictactoe::transformStringStateBoardToStateBoard($expectedStateBoard);
        
        $this->assertEquals($expectedStateBoard, $GameController->tictactoe->board->state);
    }
    
    public function test_if_I_can_save_and_load_a_specific_game_in_a_file () {
        
        
        $GameController = GameController::create();
        $tictactoe = $GameController->createNewTictactoe(5);
        $tictactoe->board->state = [
            ['1','0','1','1','2'],
            ['1','0','0','0','2'],
            ['1','0','1','1','1'],
            ['2','0','2','1','2'],
            ['1','0','1','1','2'],
        ];
        $expectedStateBoard = $tictactoe->board->state = Tictactoe::transformStringStateBoardToStateBoard($tictactoe->board->state);
        $GameController->saveTictactoe(__DIR__ . "/test_if_I_can_save_and_load_specific_game_in_a_file.csv");
        unset($GameController);
        $GameController = GameController::create();
        $GameController->loadTictactoe(__DIR__ . "/test_if_I_can_save_and_load_specific_game_in_a_file.csv");

        $this->assertEquals($expectedStateBoard,$GameController->tictactoe->board->state);
    }

    function test_if_I_can_next_State ()
    {
        $g = GameController::create();
        $g->createNewTictactoe();
        $this->assertEquals(1, $g->tictactoe->currentPlayer);

        $g->run(Tictactoe::createCaseBoard([0,0]));
        $this->assertEquals(2, $g->tictactoe->currentPlayer);
        $expectedState = Tictactoe::transformStringStateBoardToStateBoard(
            Tictactoe::transformStringToStringStateBoard("X........")
        );
        $this->assertEquals($expectedState, $g->tictactoe->board->state);

        $g->run(Tictactoe::createCaseBoard([0,2]));
        $this->assertEquals(1, $g->tictactoe->currentPlayer);
        $expectedState = Tictactoe::transformStringStateBoardToStateBoard(
            Tictactoe::transformStringToStringStateBoard("X.O......")
        );
        $this->assertEquals($expectedState, $g->tictactoe->board->state);

        $g->run(Tictactoe::createCaseBoard([2,0]));
        $this->assertEquals(2, $g->tictactoe->currentPlayer);
        $expectedState = Tictactoe::transformStringStateBoardToStateBoard(
            Tictactoe::transformStringToStringStateBoard("X.O...X..")
        );
        $this->assertEquals($expectedState, $g->tictactoe->board->state);

    }
}
    
