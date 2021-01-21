<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Player;
use App\Entity\Tictactoe;

final class TictactoeTest extends TestCase {

    public function test_if_I_can_handle_a_new_tictactoe_correct_instance() :void
    {
        $tictactoe = Tictactoe::create();
    
        $this->assertInstanceOf(Tictactoe::class, $tictactoe);
        $this->I_can_handle_a_correct_board($tictactoe, boardExpectedSize:3);
        $this->I_can_find_two_default_players($tictactoe);
        unset($tictactoe);
        error_log("TEST BOARD DEFAULT [OK]");

        $boardExpectedSizes = [3, 5, 10, 20];

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

    public function createStringStateBoard (int $size = 3) {

        foreach (range(0, $size-1) as $r)
            foreach (range(0, $size-1) as $c) 
                $virginStateBoard[$r][$c] = '.';
        return $virginStateBoard;
    }

    public function I_can_handle_a_correct_board (Tictactoe $tictactoe, int $boardExpectedSize) {

        foreach (range(0,$boardExpectedSize-1) as $r)
            foreach (range(0,$boardExpectedSize-1) as $c) 
                $boardExpectedState[$r][$c] = Tictactoe::createCaseBoard([$r, $c]);

        $this->assertEquals($boardExpectedState, $tictactoe->board->state, "TAB ERREUR");
    }

    public function I_can_find_two_default_players (Tictactoe $tictactoe) {

        $expectedPlayer1 = new Player (1, "Player 1", "X", $tictactoe);
        $expectedPlayer2 = new Player (2, "Player 2", "O", $tictactoe); 
        $expectedPlayers[1] = $expectedPlayer1;
        $expectedPlayers[2] = $expectedPlayer2;

        $this->assertEquals($expectedPlayers, $tictactoe->players);
    }

    public function test_if_I_can_t_show_an_incorrect_board_of_size_1 () {

        $this->expectException("UnexpectedValueException");
        $t=Tictactoe::create(1);
    }

    public function test_if_I_can_t_show_an_incorrect_board_of_size_neg_3 () {

        $this->expectException("UnexpectedValueException");
        $t=Tictactoe::create(-3);
    }

    public function test_if_I_can_t_show_an_incorrect_board_of_size_no_int () {

        $this->expectException("UnexpectedValueException");
        $t=Tictactoe::create("toto");
    }

   public function test_transformStringToStringStateBoard ()
   {
        $expectedStateBoard = [
            ['X','X','X'],
            ['X','O','X'],
            ['X','X','X']
        ];
    
        Tictactoe::create();
        $board = Tictactoe::transformStringToStringStateBoard('XXXXOXXXX');

        $this->assertEquals($expectedStateBoard, $board);
   }

   public function test_transformStringStateBoardToStateBoard () {

        $expectedStateBoard = $this->createStringStateBoard();
        $expectedStateBoard[1][1] = 'O';
        $expectedStateBoard[1][2] = 'X';

        $board = Tictactoe::transformStringStateBoardToStateBoard($expectedStateBoard);

        $this->assertEquals($expectedStateBoard, $board);
   }
   
    public function test_if_I_can_show_a_string_specified_board () {

        $t=Tictactoe::create("XO.X2XXX0");
        $expectedStateBoard = [
            ['X','O','.'],
            ['X','O','X'],
            ['X','X','.']
        ];
        $this->assertEquals($expectedStateBoard, $t->board->state);
    }

    public function test_if_I_can_show_an_2dStringArray_specified_board () {

        $t=Tictactoe::create( [
            ['X','O','.'],
            ['X','O','X'],
            ['X','X','.']
        ]);

        $expectedStateBoard = [
            ['X','O','.'],
            ['X','O','X'],
            ['X','X','.']
        ];
        $this->assertEquals($expectedStateBoard, $t->board->state);
    }

    public function test_if_there_is_a_winner () {
 
        $tictactoe = Tictactoe::create();
        $board = $tictactoe->board;

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
                ['X', 'X', 'X', 'X'],
                ['X', 'X', 'O', 'O']
            ]
        );
        $this->assertEquals($expectedResult, $result);
    }

// }
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