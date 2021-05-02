<?php

const SESSIONS = "lib/functions_session.php";
const TEMP_ENG = "lib/tpl_engine.php";
const TEMPLATE = "templates/tpl_main.php";
const DATABASE = "lib/connect.db.php";

$day = date('jS');
$month = date('F');
$year = date('Y');
$hrs = date('h:i').' '.date('A');
$time = $day.' '.'of'.' '.$month.' '.$year.' '.$hrs;

$etMagnis = "Et magnis dis parturient montes
nascetur ridiculus mus. Senectus et netus et malesuada. Ut sem nulla pharetra
diam sit amet. Eleifend quam adipiscing vitae proin sagittis nisl rhoncus.
Vitae tortor condimentum lacinia quis vel eros donec ac. Est ante in nibh mauris cursus mattis molestie.
Ipsum dolor sit amet consectetur adipiscing elit ut aliquam. Dignissim diam quis enim lobortis
scelerisque fermentum dui faucibus in.Viverra mauris in aliquam sem fringilla ut morbi tincidunt. 
Enim nec dui nunc mattis enim ut.<br><br>";

$lorem = "Lorem ipsum dolor sit amet, consectetur adipiscing elit,
sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.<br><br>";

?>
