<?php

use App\Entity\Player;
use App\Entity\Tictactoe;
use PHPUnit\Framework\TestCase;

final class TictactoeTest extends TestCase {

    public function test_if_I_can_handle_a_tictactoe_correct_instance() :void
    {
        $tictactoe = Tictactoe::create();
    
        $this->assertInstanceOf(Tictactoe::class, $tictactoe);
        $this->I_can_handle_a_correct_board($tictactoe, boardExpectedSize:3);
        $this->I_can_find_two_default_players($tictactoe);
        unset($tictactoe);
        error_log("TEST BOARD DEFAULT [OK]");

        $boardExpectedSizes = [3, 5];

        foreach ($boardExpectedSizes as $boardExpectedSize)
        {
            $tictactoe = Tictactoe::create($boardExpectedSize);
            $this->assertInstanceOf(Tictactoe::class, $tictactoe);
            $this->I_can_handle_a_correct_board($tictactoe, $boardExpectedSize);
            $this->I_can_find_two_default_players($tictactoe);
            unset($tictactoe);
            error_log("TEST BOARD SIZE " . $boardExpectedSize . " [OK]");
        }
    }

    public function I_can_handle_a_correct_board (Tictactoe $tictactoe, int $boardExpectedSize) {

        $boardExpectedState = new SplFixedArray($boardExpectedSize);
        for ($i=0; $i<$boardExpectedSize; $i++) {
            $boardExpectedState[$i] = new SplFixedArray($boardExpectedSize);
        }
        $boardExpectedSState = str_pad('', $boardExpectedSize, '.', STR_PAD_RIGHT);

        $board = $tictactoe->getBoard();
        $this->assertEquals($boardExpectedState, $board->getState());
        $this->assertEquals($boardExpectedSState, $board->getSState());
    }

    public function I_can_find_two_default_players (Tictactoe $tictactoe) {

        $expectedPlayer1 = new Player (1, "Player 1", "X");
        $expectedPlayer2 = new Player (2, "Player 2", "O"); 
        $expectedPlayers[1] = $expectedPlayer1;
        $expectedPlayers[2] = $expectedPlayer2;

        $players = $tictactoe->getPlayers();

        $this->assertEquals($expectedPlayers, $players);
    }

    public function test_if_I_can_t_show_an_incorrect_board () {

        $boardUnexpectedSizes = [2, -12, "toto", 3.5];
        
        foreach ($boardUnexpectedSizes as $boardUnexpectedSize)
            $this->assertNull(Tictactoe::create($boardUnexpectedSize));
    }

    // TODO
    // public function test_if_players_can_play () {

    //     $tictactoe = Tictactoe::create();
    //     $player1 = $tictactoe->getPlayer1();
    // }

    public function test_if_there_is_a_winner () {
 
        $tictactoe = Tictactoe::create();
        $board = $tictactoe->getBoard();
        $board->setSState("XXXOXOOOX");

        $expectedResult = "X";
        $result = $tictactoe->andTheWinnerIs("XXXOXOOOX");
        $this->assertEquals($expectedResult, $result);

        $expectedResult = "O";
        $result = $tictactoe->andTheWinnerIs("XXOOOOOOX");
        $this->assertEquals($expectedResult, $result);

        $expectedResult = "X";
        $result = $tictactoe->andTheWinnerIs("XOOOXOOOX");
        $this->assertEquals($expectedResult, $result);

        $expectedResult = "X";
        $result = $tictactoe->andTheWinnerIs("OOXOXOXOO");
        $this->assertEquals($expectedResult, $result);

        $expectedResult = "O";
        $result = $tictactoe->andTheWinnerIs("OOOOXOXOOXOX");
        $this->assertEquals($expectedResult, $result);

        $expectedResult = "X";
        $result = $tictactoe->andTheWinnerIs("XXXOXOOOX");
        $this->assertEquals($expectedResult, $result);

        $expectedResult = "InProgress";
        $result = $tictactoe->andTheWinnerIs("X....X.X.");
        $this->assertEquals($expectedResult, $result);

        $expectedResult = "InProgress";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['X', 'X', '.'],
                ['O', '.', 'O'],
                ['X', 'X', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);

        
    }

    function test_if_in_progress () {

        $tictactoe = Tictactoe::create();
        $expectedResult = "InProgress";
        $result = $tictactoe->andTheWinnerIs("XOOO.X.X.");
        $this->assertEquals($expectedResult, $result);
    }

    function test_if_winner_array () {

        $tictactoe = Tictactoe::create();
        $expectedResult = "InProgress";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['X', 'X', '.'],
                ['.', '.', 'O'],
                ['X', 'X', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

    function test_if_winner_array_2 () {

        $tictactoe = Tictactoe::create();
        $expectedResult = "X";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['X', 'X', '.'],
                ['X', '.', 'O'],
                ['X', 'X', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

    function test_if_winner_array_3 () {

        $tictactoe = Tictactoe::create();
        $expectedResult = "O";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['X', 'X', '.'],
                ['O', 'O', 'O'],
                ['X', 'X', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

    function test_if_winner_array_4 () {

        $tictactoe = Tictactoe::create();
        $expectedResult = "O";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['O', 'X', '.'],
                ['O', 'O', '.'],
                ['X', 'X', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

    function test_if_winner_array_5 () {

        $tictactoe = Tictactoe::create();
        $expectedResult = "InProgress";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['O', 'X', 'O'],
                ['O', '.', '.'],
                ['X', 'X', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

    function test_if_winner_array_6 () {

        $tictactoe = Tictactoe::create(4);
        $expectedResult = "X";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['O', 'X', 'O', 'X'],
                ['O', '.', '.', 'X'],
                ['X', 'X', 'O', 'X'],
                ['X', 'X', 'O', 'X']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

    function test_if_winner_array_7 () {

        $tictactoe = Tictactoe::create(4);
        $expectedResult = "O";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['O', 'X', 'O', 'X'],
                ['O', 'O', '.', 'X'],
                ['X', 'X', 'O', 'X'],
                ['X', 'X', 'O', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

    function test_if_winner_array_8 () {

        $tictactoe = Tictactoe::create(4);
        $expectedResult = "X";
        $result = $tictactoe->andTheWinnerIs(
            [
                ['O', 'X', 'O', 'X'],
                ['O', 'O', 'X', 'X'],
                ['X', 'X', '.', 'X'],
                ['X', 'X', 'O', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

//TODO
//     public function test_if_there_are_multiple_winners () {
//         $tictactoe = Tictactoe::create(4);
//         $expectedResult = "Xwin2";
//         $result = $tictactoe->andTheWinnerIs(
//             [
//                 ['X', 'X', 'X', 'X'],
//                 ['O', 'O', 'X', 'X'],
//                 ['X', 'X', '.', 'X'],
//                 ['X', 'X', 'O', 'O']
//             ]
//         );
//         $this->assertEquals($expectedResult, $result);
//     }
}