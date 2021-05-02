<?php
error_reporting(E_ALL);
include("lib/library.php");
include(DATABASE);
include(SESSIONS);
validate_session("user");
include(TEMP_ENG);

$link = mysqli_connect($server, $user, $password, $database);

$message = "";

// function to print out a table of employees and employees deletion form 
function listEmployees($link) {
    $query = "SELECT agent_name, agent_number FROM agents";
    $results = mysqli_query($link,$query);
    $table = '<input class="write" type="text" id="myInput" onkeyup="searchEmployeesName()" placeholder="Search for names...">&nbsp&nbsp&nbsp&nbsp<input class="write" type="text" id="myIDInput" onkeyup="searchEmployeesID()" placeholder="Search for ID..."><br>'.
    "<table id='myTable'><tr><th onclick='sortTable(0)'>Employee name:</th><th onclick='sortTable(1)'>Employee ID:</th></tr>";
    while($row = mysqli_fetch_array($results, MYSQLI_BOTH)) {
    $table = $table."<tr><td>".$row["agent_name"]."</td><td>".$row["agent_number"]."</td></tr>";
    }
    $tableAndDelete = $table."</table><p></p><form action='employees.php' method='POST'><label for='delete'>Delete employee:</label>&nbsp&nbsp&nbsp&nbsp<select class='write' name = 'delete'>";
    $query = "SELECT agent_name FROM agents ORDER BY agent_name ASC";
    $results = mysqli_query($link,$query);
    while($row = mysqli_fetch_array($results, MYSQLI_BOTH)) {
        $tableAndDelete = $tableAndDelete."<option value='".$row['agent_name']."'>".$row['agent_name']."</option>";
    }
    $tableAndDelete = $tableAndDelete."</select>&nbsp&nbsp&nbsp&nbsp<input class='but' type='submit' id='delete' value='Delete' onclick='deleteAlert()'></form>";
    return $tableAndDelete;
    }

// function to create a form for employees registration 
function addRegistration() {
    $form = "<form action='employees.php' method='POST'>
    <label for='name'>Employee name:</label>
    <input class='write' type='text' name='name' id='name'>
    <label for='number'>Employee ID:</label>
    <input class='write' type='text' name='number' id='number' pattern='.{6}' title='Field must be 6 characters long' placeholder='6 digits'>         
    <input class='but' type='submit' id='register' value='Register' onclick='validateAlert()'>
     </form>";
     return $form;
}

// function to write new employees name and id to database
function writeToTable($link, $name, $number) {
    $query = "INSERT INTO agents (agent_name, agent_number) VALUES (?, ?)";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "si", $name, $number);
    mysqli_stmt_execute($stmt);
}

// function to delete employees from database according to user selection 
function deleteEmployee($link, $delete) {
    $query = "DELETE FROM agents WHERE agent_name = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "s", $delete);
    mysqli_stmt_execute($stmt);
    $query = "DELETE FROM scores WHERE agent = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "s", $delete);
    mysqli_stmt_execute($stmt);
}

// function to sanitize user input to the form 
function sanitizeInput($entry, $link) {
    $input = stripslashes($entry);
    $input = htmlspecialchars($input); 
    $input = htmlentities($input);
    $input = strip_tags($input);
    $input = mysqli_real_escape_string($link, $input);
    return $input;
}

if (($_SERVER["REQUEST_METHOD"] == "POST") && (!isset($_POST['delete']))) {

// user input validation and sanitizing before processing
    $name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST["number"], FILTER_SANITIZE_NUMBER_INT);
    $name = sanitizeInput($name, $link);
    $number = sanitizeInput($number, $link);

    if (!preg_match("/^[[:alpha:] ]+$/",$name)) {
        $message = "&#9888; Entered name is invalid.";
    }
    else if ((strlen($number) < 6) || (strlen($number) > 6) || (!is_numeric($number))) {
        $message = "&#9888; Entered ID is invalid.";
    }
    else {
        writeToTable($link, $name, $number);
    }
}

// check for user prompt to delete an employee through the form 
else if (($_SERVER["REQUEST_METHOD"] == "POST") && (isset($_POST['delete']))) {
    $delete = filter_var($_POST["delete"], FILTER_SANITIZE_STRING);
    $delete = sanitizeInput($delete, $link);
    deleteEmployee($link, $delete);
    $message = "&#9888; An employee was permanently deleted.";
}

    $t = new Template(TEMPLATE);

    $t -> assign("admin", adminLink());
    $t -> assign("user", "&#128100; User: ".$_SESSION['name']);
    $t -> assign("title", "Employees"); //title of the page
    $t -> assign("title1", "Employees management section");
    $t -> assign("subtitle1", "Register a new employee:");
    $t -> assign("error1", "<span class='error'>$message</span>");
    $t -> assign("content", addRegistration());
    $t -> assign("form", "Searchable table of all the empoyees.<br>"."To sort the employees table in ascendent or descendent order please click on the header.");
    $t -> assign("table", listEmployees($link));
    $t -> assign("title2", "Latest Updates:");
    $t -> assign("subtitle2", "Now employees are easy to add...");
    $t -> assign("sidecontent1", $lorem);
    $t -> assign("subtitle3", "...and to remove");
    $t -> assign("sidecontent2", $etMagnis);

    echo $t -> render();

    mysqli_close($link);
    ?>
