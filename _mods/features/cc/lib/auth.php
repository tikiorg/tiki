<?php

class auth {

  var $id;
  var $auth;

  var $authtype;
  var $email;
  var $account;  
  var $username;
  
  function auth($user='') {
      $this->auth = false; // Assume user is not authenticated 

	if ($user) {
		global $userlib;
		$userinfo = $userlib->get_user_info($user);
	  $this->id = $userinfo['userId'];
	  $this->username = $userinfo['realname'];
    $this->email = $userinfo['email'];
    $this->account = $user;
	  $this->auth = true;
	}

  }


  function getUsername() {
      return $this->username;
    }

  function getUseraccount() {
      return $this->account;
    }
      
  function getUserid() {
      return $this->id;
    }
        
}
?>
