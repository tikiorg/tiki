<?php
// Initialization
require_once('tiki-setup.php');
require_once('lib/tikilib.php'); # httpScheme()
include_once('lib/registration/registrationlib.php');
include_once('lib/notifications/notificationlib.php');
include_once('lib/webmail/tikimaillib.php');
include_once('lib/userprefs/userprefslib.php');

// Permission: needs p_register
if($allowRegister != 'y') {
  header("location: index.php");
  exit;
  die;
}

$smarty->assign('showmsg','n');

//get custom fields
$customfields = array();
$customfields = $userprefslib->get_userprefs('CustomFields');
$smarty->assign_by_ref('customfields', $customfields);
		

if(isset($_REQUEST["register"])) {
    // $registrationlib->save_registration();
    $data = array('user'=> $_REQUEST['name'], 'mail_site'=>$_SERVER["SERVER_NAME"]);
    $notificationlib->raise_event("user_registers", $data, "tiki-register.php");
} else {
    $registrationlib->registration_form();
}

$smarty->assign('mid','tiki-register.tpl');
$smarty->display("tiki.tpl");

?>
