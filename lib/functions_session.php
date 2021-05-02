<?php
include('settings_session.php');

const LOGIN_PAGE = "http://enos.itcollege.ee/~carrod/prueba2/index.php";
//const LOGIN_PAGE = "https://enos.itcollege.ee/~lumizz/prova/index.php";
const HOME = "../home.php";
const ADMIN_AREA = "../admin_area.php";

// sanitize the input that will be used in the sql query, the Username.
function sanitize_input_var($var){
    global $link_login;
    $var= stripslashes($var);
    $var = htmlentities($var);
    $var = strip_tags($var);
    $var = trim($var);
    $var = $link_login ->real_escape_string($var);
    return $var;
}

/* bring all data received by index.php (login page) */
function get_post_data() {
    $pin = "";
    $username="";
    if(isset($_POST['pass']) && isset($_POST['un'])) {
        $pin = $_POST['pass'];
        $username = ($_POST['un']);
        $arr_login=[$username=>$pin];
    }
    return $arr_login;
}

/* Compare inserted data if equal return name. */
function validate_user_and_pass() {
    $user_pin = get_post_data();
    $user_key = array_keys($user_pin);
    $check_name = $user_key[0];
    $check_password = $user_pin[$check_name];
    $array_data_user = get_user($check_name);
    
    if (($array_data_user->name == $check_name) && ($array_data_user->password == $check_password)&& ($check_name!= null)) {
        $name = $check_name;
    }
    return $name;
}
//check if user is admin or not
function is_user_an_admin() {
    $array_data_user = get_user(validate_user_and_pass());
    if ($array_data_user->is_admin == 1) {
        $true_or_false = true;
    } else {
        $true_or_false = false;
    }
    return $true_or_false;
}

function login() {
    $user_valido = validate_user_and_pass();
    if((!is_null($user_valido)) && (is_user_an_admin()==false)) {
        $_SESSION['login_date'] = time();
        $_SESSION['name'] = $user_valido;
        goto_page(HOME);
    }
    if((!is_null($user_valido)) && (is_user_an_admin()==true)){
        $_SESSION['login_date'] = time();
        $_SESSION['name'] = $user_valido;
        $_SESSION['admin'] = $user_valido;
        goto_page(ADMIN_AREA);
    }
    else{
        $_SESSION['error'] = 1;
        goto_page(LOGIN_PAGE);
    }
}

function logout() {
    global $link_login;
    $link_login->close();
    session_unset();
    session_destroy();
    goto_page(LOGIN_PAGE);
}

function obtain_last_access() {
    $last_access = 0;
    if(isset($_SESSION['login_date'])) {
        $last_access = $_SESSION['login_date'];
    }
    return $last_access;
}

function session_active() {
    $active_state = False;
    $last_access = obtain_last_access();

    /*Set the max limit of inactivity 300 seconds*/
    $limit_last_access = $last_access + 300;

    /*Here make the comparisson. If the last iteraction is more than 120 seconds
    then the session ends;*/
    if($limit_last_access > time()) {
        $active_state = True;
        # update the timestamp
        $_SESSION['login_date'] = time();
    }
    return $active_state;
}

function validate_session($var) {
    if(!session_active()) {
        setcookie("restart", "1", time()+180, "/~carrod");//I create a cookie because with logout destroy the session.
        logout();
    }

    if($var == "user"){
        if (!isset($_SESSION['name'])) {
            logout();
        }
    } else if($var == "admin"){
        if (!(isset($_SESSION['name']) && ($_SESSION['admin']))) {
            logout();
        }

    }
}

function goto_page($pagina) {
    header("Location: $pagina");
}
//function to display admin area if the user is admin or not
function adminLink() {
    $adminlink = "<li><a href='admin_area.php'><b>Admin</b></a></li>";
    $vacuum = " ";

    if (isset($_SESSION['admin'])) {
        return $adminlink;

    } else {
        return $vacuum;
    }       
}

?>
