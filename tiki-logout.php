<?php
// Initialization
require_once('tiki-setup.php');
// go offline in Live Support
if($feature_live_support == 'y') {
	include_once('lib/live_support/lslib.php');
	if ($lslib->get_operator_status($user) != 'offline') {
		$lslib->set_operator_status($user,'offline');
	}
}
setcookie('tiki-user','',-3600);
$userlib->user_logout($user);
session_unregister('user');
unset($_SESSION['user']);
session_destroy();
unset($user);
header("location: $tikiIndex");
exit;
?>
