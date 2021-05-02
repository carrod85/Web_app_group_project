<?php
    error_reporting(E_ALL);
    include("lib/functions_session.php");

//retrieved from database the user with the post variable $username, sanitized, and compare strings
if (isset($_POST['secret_username']) && isset($_POST['secret_question']) && isset($_POST['secret_answer'])) {
    $username = sanitize_input_var($_POST['secret_username']);
    $system_pin = get_user($username);
    $question = $system_pin->question;
    $answer = $system_pin->answer;
    $pin = $system_pin->password;

    if (isset($question)) {
        if (strcmp($question, $_POST['secret_question']) == 0 &&
            strcmp($answer, $_POST['secret_answer']) == 0) {
            $passResult= "Your password is " . $pin;
        } else {
            $passError= "Incorrect secret User ID/Contract Number";
        }
    } else {
        $passError= "The user". $username." doesn't exists " ;

    }
}
?>

<!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="utf-8">
        <meta name="Recover" content="Employee performance">
        <meta name="keywords" content="Employee, performance">
        <title>Employee's performance</title>
        <link rel="shorcut icon" type="image/pgn" href="img/favicon.png">
        <link rel="stylesheet" href="styles/style_index.css">
    </head>
    <body>
        <div class="main2">
            <div class="link"><a class="goback" href="index.php">Go Back</a></div>
            <!-- <p class="sign2">Recover your password by mail</p>
            <form class="form1" action="forgot.php" method="POST">
                    <input class="field" type="text" name="recoveremail" value="" placeholder="place your username">
                    <input type="submit" class="submit" name="submit" value="Retrieve password"></p>
            </form> -->
            <p class="sign">Recover your password</p>
            <form class="form1" action="recover.php" method="POST">
                    <input class="field" type="text" name="secret_username" value="" placeholder="Username">
                    <input class="field" type="text" name="secret_question" value="" placeholder="User ID">
                    <input class="field" type="text" name="secret_answer" value="" placeholder="Contract Number">
                    <input type="submit" class="submit" name="submit" value="Retrieve password">
                    <p class="error"> <?php echo $passError, $passResult?> </p>
            </form>
        </div>
    </body>
</html>