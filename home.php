<?php
error_reporting(E_ALL);
include("lib/library.php");
include(SESSIONS);
validate_session("user");
include(TEMP_ENG);

$t = new Template(TEMPLATE);

$t -> assign("admin", adminLink());
$t -> assign("user", "&#128100; Welcome, ".$_SESSION['name']."!");
$t -> assign("title", "Home"); //title of the page
$t -> assign("title1", "Welcome to 'Employees Performance Evaluation'");
$t -> assign("subtitle1", $time);
$t -> assign("content", $etMagnis.$lorem);
$t -> assign("title2", "Latest News");
$t -> assign("subtitle2", "First News");
$t -> assign("sidecontent1", $lorem);

echo $t -> render();
?>

