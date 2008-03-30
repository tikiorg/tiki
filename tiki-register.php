<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-register.php,v 1.91.2.4 2008/03/23 14:12:05 sylvieg Exp $

/**
 * Tiki registration script
 *
 * This file takes care of user registration
 *
 * @license GNU LGPL
 * @copyright Tiki Community
 * @date created: 2002/10/8 15:54
 * @date last-modified: $Date: 2008/03/23 14:12:05 $
 */

// Initialization
require_once('tiki-setup.php');
include_once('lib/registration/registrationlib.php');
include_once('lib/notifications/notificationlib.php');

// Permission: needs p_register and not to be a slave
if ($prefs['allowRegister'] != 'y' || ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster']))) {
  header("location: index.php");
  die;
}

$smarty->assign('showmsg','n');
// novalidation is set to yes if a user confirms his email is correct after tiki fails to validate it
if (!isset($_REQUEST['novalidation'])) {
	if (!empty($_REQUEST['trackit']))
		$novalidation = 'yes'; // the user has already confirmed manually that SnowCheck is not working
	else
		$novalidation = '';
} else {
	$novalidation = $_REQUEST['novalidation'];
}

//get custom fields
$customfields = array();
$customfields = $registrationlib->get_customfields();
$smarty->assign_by_ref('customfields', $customfields);
		
$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
$nbChoiceGroups = 0;
$mandatoryChoiceGroups = true;
foreach ($listgroups['data'] as $gr) {
	if ($gr['registrationChoice'] == 'y') {
		++$nbChoiceGroups;
		$theChoiceGroup = $gr['groupName'];
		if ($gr['groupName'] == 'Registered')
			$mandatoryChoiceGroups = false;
	}
}
if ($nbChoiceGroups) {
	$smarty->assign('listgroups', $listgroups['data'] );
	if ($nbChoiceGroups == 1) {
		$smarty->assign_by_ref('theChoiceGroup', $theChoiceGroup);
	}
}

if(isset($_REQUEST['register']) && !empty($_REQUEST['name']) && (isset($_REQUEST['pass']) || isset($_SESSION['openid_url']))) {
  check_ticket('register');
  if($novalidation != 'yes' and ($_REQUEST["pass"] <> $_REQUEST["passAgain"]) and !isset($_SESSION['openid_url'])) {
    $smarty->assign('msg',tra("The passwords don't match"));
    $smarty->display("error.tpl");
    die;
  }

  if($userlib->user_exists($_REQUEST["name"])) {
    $smarty->assign('msg',tra("User already exists"));
    $smarty->display("error.tpl");
    die;
  }
  
  if($prefs['rnd_num_reg'] == 'y') {
  	if (!isset($_SESSION['random_number']) || $_SESSION['random_number']!=$_REQUEST['antibotcode']) {
    $smarty->assign('msg',tra("Wrong registration code"));
    $smarty->display("error.tpl");
    die;	
  	}
  }
  
  // VALIDATE NAME HERE
  $n = strtolower($_REQUEST['name']);
  if($n =='admin' || $n == 'anonymous' || $n == 'registered' || $n == strtolower(tra('Anonymous')) || $n == strtolower(tra('Registered'))) {
    $smarty->assign('msg',tra("Invalid username"));
    $smarty->display("error.tpl");
    die;
  }
  
  if(strlen($_REQUEST["name"])>200) {
    $smarty->assign('msg',tra("Username is too long"));
    $smarty->display("error.tpl");
    die;
  }
  
  if(strstr($_REQUEST["name"],' ')) {
    $smarty->assign('msg',tra("Username cannot contain whitespace"));
    $smarty->display("error.tpl");
    die; 	
  }

  if($prefs['lowercase_username'] == 'y') {
    if(ereg("[[:upper:]]", $_REQUEST["name"])) {
      $smarty->assign('msg',tra("Username cannot contain uppercase letters"));
      $smarty->display("error.tpl");
      die;
    }
  }

  //FALTA DEFINIR VALORES PADRÕES PARA AS DUAS VARIÁVEIS!!!
  if(strlen($_REQUEST["name"])<$prefs['min_username_length']) {
    $smarty->assign('msg',tra("Username must be at least").' '.$prefs['min_username_length'].' '.tra("characters long"));
    $smarty->display("error.tpl");
    die; 	
  }

  if(strlen($_REQUEST["name"])>$prefs['max_username_length']) {
    $smarty->assign('msg',tra("Username cannot contain more than").' '.$prefs['max_username_length'].' '.tra("characters"));
    $smarty->display("error.tpl");
    die; 	
  }

  $polerr = $userlib->check_password_policy($_REQUEST["pass"]);
  if ( !isset($_SESSION['openid_url']) && (strlen($polerr)>0) ) {
    $smarty->assign('msg', $polerr);
    $smarty->display("error.tpl");
    die;
  }
    
  if (!preg_match($patterns['login'],$_REQUEST["name"])) {
    $smarty->assign('msg',tra("Invalid username"));
    $smarty->display("error.tpl");
    die;
  }
  
  // Check the mode
  if($prefs['useRegisterPasscode'] == 'y') {
    if($_REQUEST['passcode']!=$prefs['registerPasscoder']) {
      $smarty->assign('msg',tra("Wrong passcode you need to know the passcode to register in this site"));
      $smarty->display("error.tpl");
      die;
    }
  }
  if ($nbChoiceGroups > 0 && $mandatoryChoiceGroups && empty($_REQUEST['chosenGroup'])) {
      $smarty->assign('msg',tra('You must choose a group'));
      $smarty->display("error.tpl");
      die;
    }	  
  
	if ($prefs['login_is_email'] == 'y') {
		if (empty($_REQUEST['novalidation']) || $_REQUEST['novalidation'] != 'yes') {
			$_POST['email'] = $_REQUEST['email'] = $_REQUEST['name'];
		} else {
			$_POST['name'] = $_REQUEST['name'] = $_REQUEST['email'];
		}
	}

	$email_valid = 'y';
	if (!validate_email($_REQUEST["email"],$prefs['validateEmail'])) {
		$email_valid = 'n';
	} elseif ($prefs['userTracker'] == 'y') {
		$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup'])? $_REQUEST['chosenGroup']: 'Registered');
		if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
			include_once('lib/wiki-plugins/wikiplugin_tracker.php');
			$userTrackerData = wikiplugin_tracker('', array('trackerId'=>$re['usersTrackerId'], 'fields'=>$re['registrationUsersFieldIds'], 'showdesc'=>'y', 'showmandatory'=>'y', 'embedded'=>'n'));
			$smarty->assign('userTrackerData', $userTrackerData);
			if (!isset($_REQUEST['trackit']) || (isset($_REQUEST['error']) && $_REQUEST['error'] == 'y')) {
				$email_valid = 'n';// first pass or error
			}
		}
  }
	
	if ($email_valid == 'y') {
		if (isset($_SESSION['openid_url'])) {
			$openid_url = $_SESSION['openid_url'];
		} else {
			$openid_url = '';
		}
		if($prefs['validateUsers'] == 'y' || (isset($prefs['validateRegistration']) && $prefs['validateRegistration'] == 'y')) {
			$apass = addslashes(md5($tikilib->genPass()));
			$userlib->send_validation_email($_REQUEST['name'], $apass, $_REQUEST['email']);
			
			$userlib->add_user($_REQUEST["name"],$apass,$_REQUEST["email"],$_REQUEST["pass"], false, 'n', $openid_url);
			if (isset($_REQUEST['chosenGroup']) && $userlib->get_registrationChoice($_REQUEST['chosenGroup']) == 'y') {
				$userlib->set_default_group($_REQUEST['name'], $_REQUEST['chosenGroup']);
			}	
			$logslib->add_log('register','created account '.$_REQUEST["name"]);
			$smarty->assign('showmsg','y');
		} else {
			$userlib->add_user($_REQUEST["name"],$_REQUEST["pass"],$_REQUEST["email"],'', false, 'n', $openid_url);
			if (isset($_REQUEST['chosenGroup']) && $userlib->get_registrationChoice($_REQUEST['chosenGroup']) == 'y') {
				$userlib->set_default_group($_REQUEST['name'], $_REQUEST['chosenGroup']);
			}			
			$logslib->add_log('register','created account '.$_REQUEST["name"]);
			$smarty->assign('msg',$smarty->fetch('mail/user_welcome_msg.tpl'));
			$smarty->assign('showmsg','y');
		}

		// save default user preferences
		$tikilib->set_user_preference($_REQUEST['name'], 'theme', $prefs['style']);
		$tikilib->set_user_preference($_REQUEST['name'], 'userbreadCrumb', $prefs['users_prefs_userbreadCrumb']);
		$tikilib->set_user_preference($_REQUEST['name'], 'language', $prefs['users_prefs_language']);
		$tikilib->set_user_preference($_REQUEST['name'], 'display_timezone', $prefs['users_prefs_display_timezone']);
		$tikilib->set_user_preference($_REQUEST['name'], 'user_information', $prefs['users_prefs_user_information']);
		$tikilib->set_user_preference($_REQUEST['name'], 'user_dbl', $prefs['users_prefs_user_dbl']);
		$tikilib->set_user_preference($_REQUEST['name'], 'diff_versions', $prefs['users_prefs_diff_versions']);
		$tikilib->set_user_preference($_REQUEST['name'], 'show_mouseover_user_info', $prefs['users_prefs_show_mouseover_user_info']);
		$tikilib->set_user_preference($_REQUEST['name'], 'email is public', $prefs['users_prefs_email_is_public']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mailCharset', $prefs['users_prefs_mailCharset']);
		$tikilib->set_user_preference($_REQUEST['name'], 'realName', '');
		$tikilib->set_user_preference($_REQUEST['name'], 'homePage', '');
		$tikilib->set_user_preference($_REQUEST['name'], 'lat', floatval(0));
		$tikilib->set_user_preference($_REQUEST['name'], 'lon', floatval(0));
		$tikilib->set_user_preference($_REQUEST['name'], 'country', '');
		$tikilib->set_user_preference($_REQUEST['name'], 'mess_maxRecords',$prefs['users_prefs_mess_maxRecords']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mess_archiveAfter', $prefs['users_prefs_mess_archiveAfter']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mess_sendReadStatus', $prefs['users_prefs_mess_sendReadStatus']);
		$tikilib->set_user_preference($_REQUEST['name'], 'minPrio',$prefs['users_prefs_minPrio']);
		$tikilib->set_user_preference($_REQUEST['name'], 'allowMsgs', $prefs['users_prefs_allowMsgs']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_pages', $prefs['users_prefs_mytiki_pages']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_blogs',$prefs['users_prefs_mytiki_blogs']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_gals', $prefs['users_prefs_mytiki_gals']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_msgs', $prefs['users_prefs_mytiki_msgs']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_tasks', $prefs['users_prefs_mytiki_tasks']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_items', $prefs['users_prefs_mytiki_items']);
		$tikilib->set_user_preference($_REQUEST['name'], 'mytiki_workflow', $prefs['users_prefs_mytiki_workflow']);
		$tikilib->set_user_preference($_REQUEST['name'], 'tasks_maxRecords', $prefs['users_prefs_tasks_maxRecords']);

		// Custom fields
		foreach ($customfields as $custpref=>$prefvalue ) {
	            
		    
		    if (isset($_REQUEST[$customfields[$custpref]['prefName']]))
				$tikilib->set_user_preference($_REQUEST["name"], $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
		}

		$emails = $notificationlib->get_mail_events('user_registers','*');
		if (count($emails)) {
			include_once("lib/notifications/notificationemaillib.php");
			$smarty->assign('mail_user',$_REQUEST["name"]);
			$smarty->assign('mail_date',$tikilib->now);
			$smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
			sendEmailNotification($emails, "email", "new_user_notification_subject.tpl", null, "new_user_notification.tpl");
		}

	}
}
$smarty->assign('email_valid',$email_valid);

ask_ticket('register');

$_VALID = tra("Please enter a valid %s.  No spaces, more than %d characters and contain %s");

$smarty->assign('_PROMPT_UNAME', sprintf($_VALID, tra("username"), $prefs['min_user_length'], "0-9,a-z,A-Z") );
$smarty->assign('_PROMPT_PASS', sprintf($_VALID, tra("password"), $prefs['min_pass_length'], "0-9,a-z,A-Z") );
$smarty->assign('min_user_length', $prefs['min_user_length']);
$smarty->assign('min_pass_length', $prefs['min_pass_length']);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// xajax
if ($prefs['feature_ajax'] == 'y') {
    require_once("lib/ajax/ajaxlib.php");
    $ajaxlib->setRequestURI('tiki-register_ajax.php');
    $ajaxlib->registerFunction('AJAXCheckUserName');
    $ajaxlib->registerFunction('AJAXCheckMail');
    $ajaxlib->processRequests(); // I don't really want a "process" function here, but if I don't call it here, it will not registerfunctions....
}

$smarty->assign('mid','tiki-register.tpl');
$smarty->display("tiki.tpl");

?>
