<?php
error_reporting(E_ALL);
include("lib/library.php");
include(SESSIONS);

$errorTiempo = false;
//If variable session login_date exists go to home
if (isset($_SESSION['login_date'])) {
     goto_page("home.php");
}
//If variable session error exists error message
if (isset ($_SESSION['error'])){
    $errorLogin = "* incorrect login - try again *";
}
// stop displaying error
if (!isset ($_SESSION['error'])){
    $errorLogin = null;
}
// Error message contained in variable cookie, when reload unset - delete content and destroy
if (isset($_COOKIE["restart"])){
    $errorTiempo=  "* connection-time overpassed *<br>* identify again *";
    unset($_COOKIE["restart"]);
    setcookie("restart", "1", time()-100000,"https://enos.itcollege.ee/~lumizz");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="index" content="Employee performance">
        <title>Employee's performance</title>
        <link rel="shorcut icon" type="image/pgn" href="img/favicon.png">
        <link rel="stylesheet" href="styles/style_index.css">
    </head>
    <body>
        <div class="main">
            <p class="sign">Sign in</p>
            <form class="form1" action="lib/init_session.php" method="POST">
                <input class="field" type="text" name="un" value="" placeholder="Username">
                <input class="field" type="password" name="pass" value="" placeholder="Password">
                <p><input type="submit" class="submit" name="submit" value="Sign in"></p>
                <p><a class="submit" href="recover.php">Password Recovery</a></p>
                <p class="error"> <?php echo $errorLogin, $errorTiempo?> </p>
            </form>
        </div>
    </body>
</html>