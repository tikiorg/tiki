<?php
// Initialization
require_once('tiki-setup.php');
// require_once('lib/tikilib.php'); # httpScheme()
// include_once('lib/webmail/tikimaillib.php');
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
    $data = array('user'=> $_REQUEST["name"], 'mail_site'=>$_SERVER["SERVER_NAME"]);
  if (!isset($feature_signal) || $feature_signal != 'y') {
    /* fake the callbacks using a hard-coded default sequence */
    include_once('lib/registration/registrationlib.php');
    if ( true && 
      // $registrationlib->callback_tikiwiki_setup_custom_fields("tiki-register.php", $data) &&
      $registrationlib->callback_tikiwiki_save_registration("tiki-register.php", $data) &&
      $registrationlib->callback_logslib_user_registers("tiki-register.php", $data) &&
      $registrationlib->callback_tikiwiki_send_email("tiki-register.php", $data ) &&
      $registrationlib->callback_tikimail_user_registers("tiki-register.php", $data )
    ) { /*success*/ } // endif: true...
  } else {
      /* TikiSignal is enabled - raise an event */
      include_once('lib/notifications/notificationlib.php');
      $notificationlib->raise_event("user_registers", $data, "tiki-register.php");
  } // endif: ! $feature_signal...
} else {
  include_once('lib/registration/registrationlib.php');
  $registrationlib->registration_form();
}

$smarty->assign('mid','tiki-register.tpl');
$smarty->display("tiki.tpl");

?>
