<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-login.php,v 1.12 2003-06-05 11:35:29 lrargerich Exp $

// Initialization
require_once('tiki-setup.php');

/*
if (!isset($_REQUEST["login"])) {
  header("location: $HTTP_REFERER");
  die;  
}
*/


if($tiki_p_admin == 'y') {
 if(isset($_REQUEST["su"])) {
    $_SESSION['user'] = $_REQUEST["username"];
    $smarty->assign_by_ref('user', $_REQUEST["username"]);
    header("location: $tikiIndex");
    die;
 }
}

$https_mode = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
$https_login_required = $tikilib->get_preference('https_login_required', 'n');

if ($https_login_required == 'y' && !$https_mode) {
	$url = 'https://' . $https_domain;
	if ($https_port != 443)
		$url .= ':' . $https_port;
	$url .= $https_prefix . $tikiIndex;
	if (SID)
		$url .= '?' . SID;
	header("Location " . $url);
	exit;
}

$user		= isset($_REQUEST['user'])			? $_REQUEST['user'] : false; 
$pass		= isset($_REQUEST['pass'])			? $_REQUEST['pass'] : false; 
$challenge	= isset($_REQUEST['challenge'])		? $_REQUEST['challenge'] : false; 
$response	= isset($_REQUEST['response'])		? $_REQUEST['response'] : false; 
$isvalid	= false;
$isdue		= false;

if ($user == 'admin' && !$userlib->user_exists('admin')) {
  if ($pass == 'admin') {
     $isvalid = true;
     $userlib->add_user('admin', 'admin', 'none');
  }  
} else {

  // Verify user is valid
  $isvalid = $userlib->validate_user($user, $pass, $challenge, $response);
  
  // If the password is valid but it is due then force the user to change the password by
  // sending the user to the new password change screen without letting him use tiki
  // The user must re-nter the old password so no secutiry risk here
  if ($isvalid) {
    $isdue = $userlib->is_due($user);
  }
}

if ($isvalid) {
  if ($isdue) {
    // Redirect the user to the screen where he must change his password.
    // Note that the user is not logged in he's just validated to change his password
    // The user must re-enter his old password so no secutiry risk involved
    $url = 'tiki-change_password.php?user='. urlencode($user) . '&amp;oldpass=' . urlencode($pass);
  } else {
	// User is valid and not due to change pass.. start session
    //session_register('user',$user);
    $_SESSION['user'] = $user;
    $smarty->assign_by_ref('user', $user);
    $url = $tikiIndex;

  // Now if the remember me feature is on and the user checked the rememberme checkbox then ...
	if($rememberme != 'disabled') {
		if(isset($_REQUEST['rme'])&&$_REQUEST['rme']=='on') {
		  $hash = $userlib->get_user_hash($_REQUEST['user']);
		  setcookie('tiki-user',$hash,$remembertime);
		}
	}

  }

} else {
  $url = 'tiki-error.php?error=' . urlencode(tra('Invalid username or password'));
}

if ($https_mode) {
	$stay_in_ssl_mode = isset($_REQUEST['stay_in_ssl_mode']) && $_REQUEST['stay_in_ssl_mode'] == 'on';

	if (!$stay_in_ssl_mode) {
		$http_domain = $tikilib->get_preference('http_domain', false);
		$http_port = $tikilib->get_preference('http_port', 80);
		$http_prefix = $tikilib->get_preference('http_prefix', '/');

		if ($http_domain) {
			$prefix = 'http://' . $http_domain;
			if ($http_port != 80)
				$prefix .= ':' . $http_port;
			$prefix .= $https_prefix;
			$url = $prefix . $url;
			if (SID)
				$url .= '?' . SID;
		}
	}
}



header('location: ' . $url);
exit;
?>