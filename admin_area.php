<?php
error_reporting(E_ALL);
include("lib/library.php");
include(DATABASE);
include(SESSIONS);
validate_session("admin");
include(TEMP_ENG);

$r_message = "";
$r_message2 = "";
$r_message3 = "";

//  to register new user
if (isset($_POST['r_name']) && isset($_POST['r_pass1']) && isset($_POST['r_pass2']) && isset($_POST['r_email']) && isset($_POST['r_q']) && isset($_POST['r_a']) && isset($_POST['admin'])) {

    $r_name = filter_var($_POST["r_name"], FILTER_SANITIZE_STRING);
    $r_name = sanitize_input_var($r_name);
    $r_pass1 = htmlspecialchars($_POST["r_pass1"]);
    $r_pass1 = sanitize_input_var($r_pass1);
    $r_pass2 = htmlspecialchars($_POST["r_pass2"]);
    $r_pass2 = sanitize_input_var($r_pass2);
    $r_email = filter_var($_POST["r_email"], FILTER_SANITIZE_EMAIL);
    $r_email = sanitize_input_var($r_email);
    $r_q = filter_var($_POST["r_q"], FILTER_SANITIZE_STRING);
    $r_q = sanitize_input_var($r_q);
    $r_a = filter_var($_POST["r_a"], FILTER_SANITIZE_STRING);
    $r_a = sanitize_input_var($r_a);
    $admin = sanitize_input_var($_POST["admin"]);
    
    if (!preg_match("/^[[:alpha:]]+$/",$r_name)) {
        $r_message = "&#9888; Entered username is Invalid.";
    }
    else if ((!isset($_POST['r_pass1'])) || (!isset($_POST['r_pass2'])) || (!isset($_POST['r_email']))) {
        $r_message = "&#9888; Please complete the form for each category.";
    }
    else if ((strlen($r_pass1) < 6) || (strlen($r_pass2) < 6)) {
        $r_message = "&#9888; Entered password is Invalid.";
    }
    else if ($r_pass1 != $r_pass2) {
        $r_message = "&#9888; Entered passwords do not match.";
    }
    else if ((strlen($r_q) != 6) || (!is_numeric($r_q))) {
        $r_message = "&#9888; Entered ID number is invalid";
    }    
    else if ((strlen($r_a) != 6) || (!is_numeric($r_a))) {
        $r_message = "&#9888; Entered contract number is invalid.";
    }

    else {
        $system_pin = get_user($r_name);
        if (empty($system_pin)) {
            $sql_insert=$link_login->prepare( "INSERT INTO users (user_name, user_password, user_mail, user_id,
             contract_number, is_admin)
                VALUES(?,?,?,?,?,?)");
                    $sql_insert->bind_param('sssssi', $r_name, $r_pass1, $r_email, $r_q, $r_a, $admin);
                    $sql_insert->execute();
                    $r_message = "&#9888; {$r_name} is succesfully loaded.";
                    $sql_insert->close();

                
        }
        else {
            $r_message = "&#9888; {$r_name} already exists.";
        }
    }
}

$formregister = '<div class="main2">
<form class="form1" action="admin_area.php" method="POST">
    <input class="write" type="text" name="r_name" id="r_name" placeholder="New Username" required>
    <br><input class="write" type="password" name="r_pass1" id="r_pass1" minlength="6" maxlength="12" placeholder="User Password" required>
    <br><input class="write" type="password" name="r_pass2" id="r_pass2" minlength="6" maxlength="12" placeholder="Re-Enter Password" required>
    <br><input class="write" type="email" name="r_email" id="r_email" placeholder="User e-mail" required>
    <br><input class="write" type="text" name="r_q" id="r_q" pattern=".{6}" title="Field must be 6 characters long" placeholder="User ID (6 digits)" required>
    <br><input class="write" type="text" name="r_a" id="r_a" pattern=".{6}"  title="Field must be 6 characters long" placeholder="Contract N.(6 digits)" required>
    <br><label for="admin"> Is admin:</label>
            <select class="write yesno" id="admin" name="admin" required>
            <option value="1">yes</option>
            <option value="0">no</option>
            </select>
    <br><br><input type="submit" class="but" name="submit" value="Register User"></p>
</form>
</div>';

//  function to delete an user
function delete_user(){
    global $name, $r_message3, $link_login;

    $system_pin = get_user($name);
    if (empty($system_pin)) {
        $r_message3 = "&#9888; {$name} does not exist.";
    } else {
        $sql_delete=$link_login->prepare( "DELETE FROM users WHERE user_name=?");
        $sql_delete->bind_param('s', $name);
        $sql_delete->execute();
        $r_message3 = "&#9888; {$name} is permanently deleted.";
        $sql_delete->close();
        
    }
}

//  function to modify an user
function modify_user(){
    global $name, $r_message2, $link_login, $pin1, $mail, $question, $answer, $admin;

    $system_pin = get_user($name);
    if (empty($system_pin)) {
        $r_message2 = "&#9888; {$name} does not exist.";
    } else {
        $sql_update=$link_login->prepare("UPDATE users SET user_password = ?, user_mail = ?, user_id=?, 
        contract_number=?, is_admin=? WHERE user_name=?");
        $sql_update->bind_param('ssssis', $pin1, $mail, $question, $answer, $admin, $name);
        $sql_update->execute();
        $sql_update->close();
        $r_message2 = "&#9888; {$name} profile is modified.";
    }
}

// function to list users for drop down selection to deleta an user 
function deleteUserList() {
    global $link_login;
    $formdelete = '<form class="form1" action="admin_area.php" method="POST"><select class="write" name="delete_user">';
    $select_users_query = "SELECT user_name FROM users ORDER BY user_name ASC";
    $result = $link_login->query($select_users_query);
    if($result->num_rows>0)
        while($row = $result->fetch_assoc()) {
            $formdelete = $formdelete."<option id='delete_user' value='".$row["user_name"]."'>".$row["user_name"]."</option>";
    }
        $formdelete = $formdelete.'</select><br><input type="submit" class="but" name="submit" id="delete_user" value="Delete User">
        </form>';
    return $formdelete;
}

// function to modify an existing user
function modifyUserList() {
    global $link_login;
    $formmodify = '<form class="form1" action="admin_area.php" method="POST"><select class="write" name="modify_name">';
    $select_users_query = "SELECT user_name FROM users ORDER BY user_name ASC";
    $result = $link_login->query($select_users_query);
    if($result->num_rows>0)
        while($row = $result->fetch_assoc()) {
            $formmodify = $formmodify."<option id='modify_name' value='".$row["user_name"]."'>".$row["user_name"]."</option>";
    }
    $formmodify = $formmodify.'</select>
    <input class="write" type="password" name="pin1" id="pin" minlength="6" maxlength="12" placeholder="New Password" required>
    <input class="write" type="password" name="pin2" id="pin" minlength="6" maxlength="12" placeholder="Re-Enter new Password" required>
    <input class="write" type="email" name="mail" id="mail" placeholder="New e-mail" required>
    <input class="write" type="text" name="question" id="question" pattern=".{6}" placeholder="User ID (6 digits)" " title="Field must be 6 characters long" required>
    <input class="write" type="text" name="answer" id="answer" pattern=".{6}" placeholder="Contract N.(6 digits)" title="Field must be 6 characters long" required>
    <br><label for="admin"> Is admin:</label>
    <select class="write yesno" id="admin" name="admin" required>
    <option value="1">yes</option>
    <option value="0">no</option>
    </select>
    <br><input type="submit" class="but" name="submit" value="Modify User"></p>
        </form>';
    return $formmodify;
}

// if form submitted call function   
if (isset($_POST['delete_user'])) {
        $name = sanitize_input_var($_POST['delete_user']);
        delete_user();
}
if (isset($_POST['modify_name']) && isset($_POST["pin1"]) && isset($_POST["pin2"]) && isset($_POST["mail"]) && isset($_POST["question"]) && isset($_POST["answer"]) && isset($_POST["admin"])) {
        $name = sanitize_input_var($_POST['modify_name']);
        $pin1 = sanitize_input_var($_POST["pin1"]);
        $pin2 = sanitize_input_var($_POST["pin2"]);
        $mail = sanitize_input_var($_POST["mail"]);
        $question = sanitize_input_var($_POST["question"]);
        $answer = sanitize_input_var($_POST["answer"]);
        $admin = sanitize_input_var($_POST["admin"]);

        if ((!isset($_POST['pin1'])) || (!isset($_POST['pin2'])) || (!isset($_POST['mail']))) {
            $r_message2 = "&#9888; Please complete the form for each category.";
        }
        else if ((strlen($pin1) < 6) || (strlen($pin2) < 6)) {
            $r_message2 = "&#9888; Entered password is Invalid.";
        }
        else if ($pin1 != $pin2) {
            $r_message2 = "&#9888; Entered passwords do not match.";
        }
        else if ((strlen($question) != 6) || (!is_numeric($question))) {
            $r_message2 = "&#9888; Entered ID is invalid.";
        }    
        else if ((strlen($answer) != 6) || (!is_numeric($answer))) {
            $r_message2 = "&#9888; Entered contract number is invalid.";
        }
    
        else {
        modify_user();
    }
}

$downloadtickets =  "<a class='but' href='data/tickets.csv'>Click here to download now!</a>";

$t = new Template(TEMPLATE);

$t -> assign("admin", adminLink());
$t -> assign("user", "&#128100 Administrator access: ".$_SESSION['name']);
$t -> assign("title", "Admin Area"); //title of the page
$t -> assign("title1", "Admin Area");
$t -> assign("subtitle1", "Register new user for the website:");
$t -> assign("error1", "<span class='error'>".$r_message."</span>");
$t -> assign("content", $formregister);
$t -> assign("form", "Here the admin can download the tikets submitted by the users in the support page, CSV format");
$t -> assign("table", $downloadtickets);
$t -> assign("title2", "Modify User");
$t -> assign("subtitle2", "Select the username and add the new data for that username");
$t -> assign("error2", "<span class='error'>".$r_message2."</span>");
$t -> assign("sidecontent1", modifyUserList());
$t -> assign("title3", "Delete User");
$t -> assign("subtitle3", "Select the name of the user to delete");
$t -> assign("error3", "<span class='error'>".$r_message3."</span>");
$t -> assign("sidecontent2", deleteUserList());

echo $t -> render();

?>
