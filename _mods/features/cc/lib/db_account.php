<?php
require_once "dbhandle.php";

class db_account
{
  var $id = "";
  var $acct_password = "";
  var $acct_email = "";
  var $acct_name = "";
  var $acct_account = "";
  function db_account() { }

  function register($id,$account,$name,$email,$passwd) {
      $this->id = $id;
      $this->acct_account = $account;
      $this->acct_email = $email;
      $this->acct_name = $name;
      $this->acct_password = $passwd;
    }

  function loadByid($id) { 
		global $userslib,$tikilib;
		$user = $tikilib->getOne("select login from users_users where userId=$id");
		$userinfo = $userlib->get_user_info($user);
	  $this->id = $hash[id];
	  $this->acct_account = $hash['acct_account'];
	  $this->acct_name = $hash['acct_name'];
	  $this->acct_password=$hash['acct_password'];
	  $this->acct_email = $hash['acct_email'];
	  $this->last_login_date = $hash['last_login_date'];
  }    
    
    
}


?>
