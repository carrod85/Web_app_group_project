<?php
error_reporting(E_ALL);
include("lib/library.php");
include(DATABASE);
include(SESSIONS);
validate_session("user");
include(TEMP_ENG);

$link = mysqli_connect($server, $user, $password, $database);

$message = "";

// function to list employees for drop down selection and create entire form 
function listAgents($link) {
    $query = "SELECT agent_name FROM agents ORDER BY agent_name ASC";
    $results = mysqli_query($link,$query);
    $form = "";
    while($row = mysqli_fetch_array($results, MYSQLI_BOTH)) {
        $form = $form."<option value='".$row['agent_name']."'>".$row['agent_name']."</option>";
    }
    $form = createForm($form);
    return $form;
}

// function to write submitted form data to database 
function writeToTable($link, $evaluator, $name, $rate1, $rate2, $score5, $total, $date, $time) {
    $query = "INSERT INTO scores (evaluator, agent, communication, troubleshooting, documentation, total, date, time) VALUES (?, ?, ?, ?, ?, ?, '$date', ?);";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "ssiiiii", $evaluator, $name, $rate1, $rate2, $score5, $total, $time);
    mysqli_stmt_execute($stmt);
}

// function to create evaluation form with drop down list for selection
function createForm($form) {
	$finalForm = "<form action='form.php' method='POST' id='data'><label for='name'>Employee name:</label><br><select class='write' name = 'name'>";
	$finalForm = $finalForm.$form."</select><p class='scores'><b>&#10003; Scores for Communication:</b></p>
    <p>Agent followed appropriate call flow and used time of the call reasonably.</p>
    <input type='radio' name='score1' id='score1' value='100' checked='checked'><label for='score1'><i>Meets Expectations</i></label><br>
    <input type='radio' name='score1' id='score1' value='0'><label for='score1'><i>Needs Improvement</i></label>
    <p>Agent demonstrated professional manner of communication during interaction.</p>
    <input type='radio' name='score2' id='score2' value='100' checked='checked'><label for='score2'><i>Meets Expectations</i></label><br>
    <input type='radio' name='score2' id='score2' value='0'><label for='score2'><i>Needs Improvement</i></label>
    <p class='scores'><b>&#10003; Scores for Troubleshooting:</b></p>
    <p>Agent demonstrated active listening and asked relevant probing questions.</p>
    <input type='radio' name='score3' id='score3' value='100' checked='checked'><label for='score3'><i>Meets Expectations</i></label><br>
    <input type='radio' name='score3' id='score3' value='0'><label for='score3'><i>Needs Improvement</i></label>
    <p>Agent guided customer to follow appropriate and relevant troubleshooting steps.</p>
    <input type='radio' name='score4' id='score4' value='100' checked='checked'><label for='score4'><i>Meets Expectations</i></label><br>
    <input type='radio' name='score4' id='score4' value='0'><label for='score4'><i>Needs Improvement</i></label>
    <p class='scores'><b>&#10003; Scores for Documentation:</b></p>
    <p>Agent documented sufficient and relevant details of interaction in logging tool.</p>
    <input type='radio' name='score5' id='score5' value='100' checked='checked'><label for='score5'><i>Meets Expectations</i></label><br>
    <input type='radio' name='score5' id='score5' value='0'><label for='score5'><i>Needs Improvement</i></label>
    <p><button class='submit evaluation' type='submit' value='Submit'>Submit</button></p>
    </form>";
    return $finalForm;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $unwanted = array("!", "#", "$", "%", "&", "'", "*", "+", "-", "=", "?", "^", "_", "`", "{", "|", "}", "~", "@", ".", "[", "]", ",");

// evaluation form input validation and sanitizing 
    $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
    $name = str_replace($unwanted, "", $name);
    $name = htmlspecialchars($name);
    $score1 = filter_var($_POST["score1"], FILTER_SANITIZE_NUMBER_INT);
    $score2 = filter_var($_POST["score2"], FILTER_SANITIZE_NUMBER_INT);
    $score3 = filter_var($_POST["score3"], FILTER_SANITIZE_NUMBER_INT);
    $score4 = filter_var($_POST["score4"], FILTER_SANITIZE_NUMBER_INT);
    $score5 = filter_var($_POST["score5"], FILTER_SANITIZE_NUMBER_INT);

    if (!preg_match("/^[[:alpha:] ]+$/",$name)) {
        $message = "&#9888; Entered name is Invalid.";
    }
    else if((!isset($_POST['score1'])) || (!isset($_POST['score2'])) || (!isset($_POST['score3'])) || (!isset($_POST['score4'])) || (!isset($_POST['score5']))) {
        $message = "&#9888; Please complete the form for each category.";
    }
    else {
        $time = time();
        $date = date('Y-m-d');
// calculation of scores depending on the radio buttons chosen 
        $rate1 = ($score1 + $score2) / 2;
        $rate2 = ($score3 + $score4) / 2;
        $total = ($score1 + $score2 + $score3 + $score4 + $score5) / 5;
        $evaluator = $_SESSION['name'];
        writeToTable($link, $evaluator, $name, $rate1, $rate2, $score5, $total, $date, $time);
        $message = "&#9888; Evaluation form was successfully submitted.";
    }
}

$t = new Template(TEMPLATE);

$t -> assign("admin", adminLink());
$t -> assign("user", "&#128100; User: ".$_SESSION['name']);
$t -> assign("title", "Form"); //title of the page
$t -> assign("title1", "Evaluation area");
$t -> assign("subtitle1", "Here evaluators can assign different scores to the agents");
$t -> assign("error1", "<span class='error'>$message</span>");
$t -> assign("content", "Please select an employee and feel the form to evaluate him.");
$t -> assign("form", listAgents($link));
$t -> assign("title2", "Latest Updates:");
$t -> assign("subtitle2", "New Troubleshooting triggers");
$t -> assign("sidecontent1", $etMagnis);
$t -> assign("subtitle3", "Changes in Communication scores");
$t -> assign("sidecontent2", $lorem.$etMagnis);

echo $t -> render();

mysqli_close($link);
?>


