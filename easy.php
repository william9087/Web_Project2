<?php

session_start();

$letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$WON = false;

// temp variables for testing

$guess = "HANGMAN";
$maxLetters = strlen($guess) - 1;
$responses = ["H", "G", "A"];


// Live variables here


// ALl the body parts
$bodyParts = ["gallow0", "gallow1", "gallow2", "gallow3", "gallow4", "gallow5", "gallow6"];


// Random words for the game and you to guess
$words = [
    "WEB", "FLY", "APPLE", "CLASS", "BUTTER",
    "CAT", "SING"
];


function getCurrentPicture($part)
{
    return "./pics/" . $part . ".png";
}


function startGame()
{
}

// restart the game. Clear the session variables
function restartGame()
{
    session_destroy();
    session_start();
}

// Get all the hangman Parts
function getParts()
{
    global $bodyParts;
    if (!isset($_SESSION["parts"]) || !is_array($_SESSION["parts"])) {
        $_SESSION["parts"] = $bodyParts;
    }
    return $_SESSION["parts"];
}

// add part to the Hangman
function addPart()
{
    $parts = getParts();
    array_shift($parts);
    $_SESSION["parts"] = $parts;
}

// get Current Hangman Body part
function getCurrentPart()
{
    $parts = getParts();
    return $parts[0];
}

// get the current words
function getCurrentWord()
{
    //  return "HANGMAN"; // for now testing
    global $words;
    if (!isset($_SESSION["word"]) && empty($_SESSION["word"])) {
        $key = array_rand($words);
        $_SESSION["word"] = $words[$key];
    }
    return $_SESSION["word"];
}


// user responses logic

// get user response
function getCurrentResponses()
{
    return isset($_SESSION["responses"]) ? $_SESSION["responses"] : [];
}

function addResponse($letter)
{
    $responses = getCurrentResponses();
    array_push($responses, $letter);
    $_SESSION["responses"] = $responses;
}

// check if pressed letter is correct
function isLetterCorrect($letter)
{
    $word = getCurrentWord();
    $max = strlen($word) - 1;
    for ($i = 0; $i <= $max; $i++) {
        if ($letter == $word[$i]) {
            return true;
        }
    }
    return false;
}

// is the word (guess) correct

function isWordCorrect()
{
    $guess = getCurrentWord();
    $responses = getCurrentResponses();
    $max = strlen($guess) - 1;
    for ($i = 0; $i <= $max; $i++) {
        if (!in_array($guess[$i],  $responses)) {
            return false;
        }
    }
    return true;
}

// check if the body is ready to hang

function isBodyComplete()
{
    $parts = getParts();
    // is the current parts less than or equal to one
    if (count($parts) <= 1) {
        return true;
    }
    return false;
}

// manage game session

// is game complete
function gameComplete()
{
    return isset($_SESSION["gamecomplete"]) ? $_SESSION["gamecomplete"] : false;
}


// set game as complete
function markGameAsComplete()
{
    $_SESSION["gamecomplete"] = true;
}

// start a new game
function markGameAsNew()
{
    $_SESSION["gamecomplete"] = false;
}



/* Detect when the game is to restart. From the restart button press*/
if (isset($_GET['start'])) {
    restartGame();
}

if (isset($_GET['back'])) {
    restartGame();
    header('Location: mode.html');
}


/* Detect when Key is pressed */
if (isset($_GET['kp'])) {
    $currentPressedKey = isset($_GET['kp']) ? $_GET['kp'] : null;
    // if the key press is correct
    if (
        $currentPressedKey
        && isLetterCorrect($currentPressedKey)
        && !isBodyComplete()
        && !gameComplete()
    ) {

        addResponse($currentPressedKey);
        if (isWordCorrect()) {
            $WON = true; // game complete
            markGameAsComplete();
        }
    } else {
        // start hanging the man :)
        if (!isBodyComplete()) {
            addPart();
            if (isBodyComplete()) {
                markGameAsComplete(); // lost condition
            }
        } else {
            markGameAsComplete(); // lost condition
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Hangman The Game</title>
    <link href="index.css" type="text/css" rel="stylesheet" />
</head>

<body>
    <img id="game_bg" src="pics/gamebg.jpg" />
    <p id="name1">T H E - H A N G M A N</p>
    <form method="get">
        <button id="game_back" type="submit" name="back">Back</button>
    </form>

    <!-- Main app display -->
    <!-- <div style="margin: 0 auto; background: #dddddd; width:900px; height:900px; padding:5px; border-radius:3px;"> -->

    <!-- Display the image here -->
    <div>
        <img id="gallow" src="<?php echo getCurrentPicture(getCurrentPart()); ?>" />

        <!-- Indicate game status -->
        <?Php if (gameComplete()) : ?>
            <!-- <h1>GAME COMPLETE</h1> -->
        <?php endif; ?>
        <?php if ($WON  && gameComplete()) : ?>
            <p id="won">You Won!</p>
        <?php elseif (!$WON  && gameComplete()) : ?>
            <p id="lost">You LOST!</p>
        <?php endif; ?>
    </div>

    <div id="keyboard">
        <div>
            <form method="get">
                <?php
                $max = strlen($letters) - 1;
                for ($i = 0; $i <= $max; $i++) {
                    echo "<button type='submit' name='kp' value='" . $letters[$i] . "'>" .
                        $letters[$i] . "</button>";
                    if ($i % 7 == 0 && $i > 0) {
                        echo '<br>';
                    }
                }
                ?>
                <br><br>
                <!-- Restart game button -->
                <button id="restart" type="submit" name="start">Restart Game</button>
            </form>
        </div>
    </div>

    <div id="letters1">
        <!-- Display the current guesses -->
        <?php
        $guess = getCurrentWord();
        $maxLetters = strlen($guess) - 1;
        for ($j = 0; $j <= $maxLetters; $j++) : $l = getCurrentWord()[$j]; ?>
            <?php if (in_array($l, getCurrentResponses())) : ?>
                <span style="font-size: 35px; border-bottom: 3px solid #000; margin-right: 5px;"><?php echo $l; ?></span>
            <?php else : ?>
                <span style="font-size: 35px; border-bottom: 3px solid #000; margin-right: 5px;">&nbsp;&nbsp;&nbsp;</span>
            <?php endif; ?>
        <?php endfor; ?>
    </div>

    </div>



</body>


</html>