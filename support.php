<?php
error_reporting(E_ALL);
include("lib/library.php");
include(SESSIONS);
validate_session("user");
include(TEMP_ENG);

$c_message = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

// user input validation and sanitizing before writing to file 
    $c_first = htmlspecialchars($_POST["c_first"]);
    $c_first = str_replace("|", "", $c_first);
    $c_last = htmlspecialchars($_POST["c_last"]);
    $c_last = str_replace("|", "", $c_last);
    $c_phone = filter_var($_POST["c_phone"], FILTER_SANITIZE_NUMBER_INT);
    $c_phone = str_replace("|", "", $c_phone);
    $c_email = filter_var($_POST["c_email"], FILTER_SANITIZE_EMAIL);
    $c_email = str_replace("|", "", $c_email);
    $c_describe = htmlspecialchars($_POST["c_describe"]);
    $c_describe = str_replace("|", "", $c_describe);

    if ((!preg_match("/^[[:alpha:]]+$/",$c_first)) || (!preg_match("/^[[:alpha:]]+$/",$c_last))) {
        $c_message = "&#9888; Entered Name is Invalid.";
    }
    else if ((!isset($_POST['c_phone'])) || (!isset($_POST['c_email'])) || (!isset($_POST['c_describe']))) {
        $c_message = "&#9888; Please complete the form for each category.";
    }
    else {
// writing support form input data to CSV file 
        $c_entry = $c_first . "|" . $c_last . "|" . $c_phone . "|" . $c_email . "|" . $c_describe . "|" . "\r\n";
        $c_tickets = fopen("data/tickets.csv", "a");
        fputcsv($c_tickets,explode("|",$c_entry));
        fclose($c_tickets);
        $c_message = "&#9888; Your ticket is successfully submitted.";
    }
}

$contactForm = '<form action="support.php" method="POST">
<label>First Name:</label><br>
<input class="write" type="text" name="c_first" id="c_first" maxlength="15" required><br>
<label>Family Name:</label><br>
<input class="write" type="text"name="c_last" id="c_last" maxlength="15" required><br>
<label>Email:</label><br>
<input class="write" type="email" name="c_email" id="c_email" required><br>
<label>Telephone Number:</label><br>
<input class="write" type="tel" name="c_phone" id="c_phone" maxlength="15" required><br>
<label>Issue with the service:</label><br>
<textarea class="write text" name="c_describe" id="c_describe" rows="3" cols="60"></textarea>
<p><button class="submit evaluation" type="submit" value="Submit">Submit</button></p>
</form>';

$t = new Template(TEMPLATE);

$t -> assign("admin", adminLink());
$t -> assign("user", "&#128100; User: ".$_SESSION['name']);
$t -> assign("title", "Support"); //title of the page
$t -> assign("title1", "Need help from the Admin?");
$t -> assign("subtitle1", "Please fill the form to be contacted soon.");
$t -> assign("error1", "<span class='error'>$c_message</span>");
$t -> assign("content", "The admin will access tickets in his dedicated area.");
$t -> assign("form", $contactForm);
$t -> assign("title2", "Contacts");
$t -> assign("subtitle2", "How to contact us:");
$t -> assign("sidecontent1", $etMagnis);

echo $t -> render();
?>
