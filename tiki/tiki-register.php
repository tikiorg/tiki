<?php
// Initialization
require_once('tiki-setup.php');
// require_once('lib/tikilib.php'); # httpScheme()
// include_once('lib/webmail/tikimaillib.php');
include_once('lib/registration/registrationlib.php');

// Permission: needs p_register
if($allowRegister != 'y') {
  header("location: index.php");
  exit;
  die;
}

$smarty->assign('showmsg','n');

//get hidden fields
$hiddenfields = array();
$hiddenfields = $registrationlib->get_hiddenfields();
$smarty->assign_by_ref('hiddenfields', $hiddenfields);

//get custom fields
$customfields = array();
$customfields = $registrationlib->get_customfields();
$smarty->assign_by_ref('customfields', $customfields);
		

if(isset($_REQUEST["register"])) {
    $data = $_POST;
    $data['user'] = $_REQUEST["name"];
    $data['mail_site'] = $_SERVER["SERVER_NAME"];
  if ((!isset($feature_signal)) || $feature_signal != 'y') {
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

$_VALID = tra("Please enter a valid %s.  No spaces, more than %d characters and contain %s");

$smarty->assign('_PROMPT_UNAME', sprintf($_VALID, tra("username"), $min_user_length, "0-9,a-z,A-Z") );
$smarty->assign('_PROMPT_PASS', sprintf($_VALID, tra("password"), $min_pass_length, "0-9,a-z,A-Z") );
$smarty->assign('min_user_length', $min_user_length);
$smarty->assign('min_pass_length', $min_pass_length);
$smarty->assign('mid','tiki-register.tpl');
$smarty->display("tiki.tpl");

?>
