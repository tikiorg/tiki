<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_username($user, $login_fallback = true, $check_user_show_realnames = true, $html_encoding = true) {
	global $userlib, $prefs;

	if ( $prefs['user_show_realnames'] == 'y' || ! $check_user_show_realnames ) {
		$details = $userlib->get_user_details($user);
		$return = $details['info']['realName'];
		unset($details);
		if ( $return == '' ) $return = $login_fallback ? $user : tra('Anonymous');
	} else $return = $user;

	if ($html_encoding) $return = htmlspecialchars($return);
	return $return;
}
