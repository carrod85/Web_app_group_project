<?php
# we leave the session started
session_start();
include "connect.db.php";

$link_login = new mysqli($server, $user, $password, $database);
if ($link_login-> connect_error)
    die ("Connection failed:". $link_login -> connect_error);

class User{
  public $name;
  public $password;
  public $mail;
  public $question;
  public $answer;
  public $is_admin;

  function __construct ($name_cons, $pass_cons,$mail_cons, $ques_cons, $ans_cons,$adm_cons){
      $this-> name = $name_cons;
      $this -> password = $pass_cons;
      $this -> mail = $mail_cons;
      $this -> question = $ques_cons;
      $this -> answer = $ans_cons;
      $this -> is_admin = $adm_cons;
  }
}

# get users  
function get_user($username)
{
    global $link_login;

    $sql = $link_login->prepare("SELECT user_name, user_password, user_mail, user_id, contract_number, is_admin
    FROM users WHERE user_name = ?");
    $sql->bind_param('s', $username);
    $sql->execute();
    $sql->bind_result($name, $pass, $mail, $id, $con, $admin);
    while ($sql->fetch()) {
        $user = new User($name, $pass, $mail, $id, $con, $admin);
        return $user;
    }
        
    $sql->close();
}
?>
