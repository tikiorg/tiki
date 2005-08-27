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
    $data = array('user'=> $_REQUEST['name'], 'mail_site'=>$_SERVER["SERVER_NAME"]);
    /* if preserve_environment {
      if ( true && 
      $registrationlib->callback_tikiwiki_setup_custom_fields( $data, "tiki-register.php" ) &&
      $registrationlib->callback_tikiwiki_save_registration( $data, "tiki-register.php" ) &&
      $registrationlib->callback_logslib_user_registers( $data, "tiki-register.php" ) &&
      $registrationlib->callback_tikiwiki_send_email( $data, "tiki-register.php" ) &&
      $registrationlib->callback_tikimail_user_registers( $data, "tiki-register.php" );
      ) { /* success 
    else { */
     $notificationlib->raise_event("user_registers", $data, "tiki-register.php");
     }
 else {
    $registrationlib->registration_form();
}

$smarty->assign('mid','tiki-register.tpl');
$smarty->display("tiki.tpl");

?>
