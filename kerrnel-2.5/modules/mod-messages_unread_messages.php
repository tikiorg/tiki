<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $user;

if ($user) {
	$modUnread = $tikilib->user_unread_messages($user);

	$smarty->assign('modUnread', $modUnread);
}

?>
