<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-register.php,v 1.74 2006-12-27 02:55:54 mose Exp $

/**
 * Tiki registration script
 *
 * This file takes care of user registration
 *
 * @license GNU LGPL
 * @copyright Tiki Community
 * @date created: 2002/10/8 15:54
 * @date last-modified: $Date: 2006-12-27 02:55:54 $
 */

// Initialization
require_once('tiki-setup.php');
// require_once('lib/tikilib.php'); # httpScheme()
// include_once('lib/webmail/tikimaillib.php');
include_once('lib/registration/registrationlib.php');
include_once('lib/notifications/notificationlib.php');

// Permission: needs p_register and not to be a slave
if ($allowRegister != 'y' || ($feature_intertiki == 'y' && !empty($feature_intertiki_mymaster))) {
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
		

if(isset($_REQUEST['register']) && !empty($_REQUEST['name']) && isset($_REQUEST['pass'])) {
  check_ticket('register');
  if($novalidation != 'yes' and ($_REQUEST["pass"] <> $_REQUEST["passAgain"])) {
    $smarty->assign('msg',tra("The passwords don't match"));
    $smarty->display("error.tpl");
    die;
  }
  if($userlib->user_exists($_REQUEST["name"])) {
    $smarty->assign('msg',tra("User already exists"));
    $smarty->display("error.tpl");
    die;
  }
  
  if($rnd_num_reg == 'y') {
  	if($novalidation != 'yes' and(!isset($_SESSION['random_number']) || $_SESSION['random_number']!=$_REQUEST['regcode'])) {
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
  
  if(strlen($_REQUEST["name"])>37) {
    $smarty->assign('msg',tra("Username is too long"));
    $smarty->display("error.tpl");
    die;
  }
  
  if(strstr($_REQUEST["name"],' ')) {
    $smarty->assign('msg',tra("Username cannot contain whitespace"));
    $smarty->display("error.tpl");
    die; 	
  }

  if($lowercase_username == 'y') {
    if(ereg("[[:upper:]]", $_REQUEST["name"])) {
      $smarty->assign('msg',tra("Username cannot contain uppercase letters"));
      $smarty->display("error.tpl");
      die;
    }
  }

  //FALTA DEFINIR VALORES PADRÕES PARA AS DUAS VARIÁVEIS!!!
  if(strlen($_REQUEST["name"])<$min_username_length) {
    $smarty->assign('msg',tra("Username must be at least").' '.$min_username_length.' '.tra("characters long"));
    $smarty->display("error.tpl");
    die; 	
  }

  if(strlen($_REQUEST["name"])>$max_username_length) {
    $smarty->assign('msg',tra("Username cannot contain more than").' '.$max_username_length.' '.tra("characters"));
    $smarty->display("error.tpl");
    die; 	
  }

  //Validate password here
  if(strlen($_REQUEST["pass"])<$min_pass_length) {
    $smarty->assign('msg',tra("Password should be at least").' '.$min_pass_length.' '.tra("characters long"));
    $smarty->display("error.tpl");
    die; 	
  }
  
  // Check this code
  if($pass_chr_num == 'y') {
    if (!preg_match("/[0-9]/",$_REQUEST["pass"]) || !preg_match("/[A-Za-z]/",$_REQUEST["pass"])) {
      $smarty->assign('msg',tra("Password must contain both letters and numbers"));
      $smarty->display("error.tpl");
      die; 	
    }
  }
  
  if (!preg_match($patterns['login'],$_REQUEST["name"])) {
    $smarty->assign('msg',tra("Invalid username"));
    $smarty->display("error.tpl");
    die;
  }
  
  // Check the mode
  if($useRegisterPasscode == 'y') {
    if(($_REQUEST["passcode"]!=$tikilib->get_preference("registerPasscode",md5($tikilib->genPass())))) {
      $smarty->assign('msg',tra("Wrong passcode you need to know the passcode to register in this site"));
      $smarty->display("error.tpl");
      die;
    }
  }
  
	$email_valid = 'y';
	if (!validate_email_syntax($_REQUEST["email"])) {
		$email_valid = 'n';
	} elseif ($userTracker == 'y') {
		$re = $userlib->get_group_info(isset($_REQUEST['chosenGroup'])? $_REQUEST['chosenGroup']: 'Registered');
		if (!empty($re['usersTrackerId']) && !empty($re['registrationUsersFieldIds'])) {
			include_once('lib/wiki-plugins/wikiplugin_tracker.php');
			$userTrackerData = $tikilib->parse_data(wikiplugin_tracker('', array('trackerId'=>$re['usersTrackerId'], 'fields'=>$re['registrationUsersFieldIds'], 'showdesc'=>'y', 'showmandatory'=>'y', 'embedded'=>'n')));
			$smarty->assign('userTrackerData', $userTrackerData);
			if (!isset($_REQUEST['trackit']) || (isset($_REQUEST['error']) && $_REQUEST['error'] == 'y')) {
				$email_valid = 'n';// first pass or error
			}
		}
  }
	
	if ($email_valid == 'y') {
		if($validateUsers == 'y' || (isset($validateRegistration) && $validateRegistration == 'y')) {
			//$apass = addslashes(substr(md5($tikilib->genPass()),0,25));
			$apass = addslashes(md5($tikilib->genPass()));
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$foo1=str_replace("tiki-register","tiki-login_validate",$foo["path"]);
			$machine =$tikilib->httpPrefix().$foo1;
			$userlib->add_user($_REQUEST["name"],$apass,$_REQUEST["email"],$_REQUEST["pass"]);
			if (isset($_REQUEST['chosenGroup']) && $userlib->get_registrationChoice($_REQUEST['chosenGroup']) == 'y') {
				$userlib->set_default_group($_REQUEST['name'], $_REQUEST['chosenGroup']);
			}	
			
			$logslib->add_log('register','created account '.$_REQUEST["name"]);
			$smarty->assign('mail_machine',$machine);
			$smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
			$smarty->assign('mail_user',$_REQUEST["name"]);
			$smarty->assign('mail_apass',$apass);
			$smarty->assign('mail_email',$_REQUEST['email']);
			include_once("lib/notifications/notificationemaillib.php");
			if (isset($validateRegistration) and $validateRegistration == 'y') {
				$smarty->assign('msg',$smarty->fetch('mail/user_validation_waiting_msg.tpl'));
				if ($sender_email == NULL or !$sender_email) {
					include_once('lib/messu/messulib.php');
					$mail_data = $smarty->fetch('mail/moderate_validation_mail.tpl');
					$mail_subject = $smarty->fetch('mail/moderate_validation_mail_subject.tpl');
					$messulib->post_message($contact_user,$contact_user,$contact_user,'',$mail_subject,$mail_data,5);
				} else {
					$mail_data = $smarty->fetch('mail/moderate_validation_mail.tpl');
					$mail = new TikiMail();
					$mail->setText($mail_data);
					$mail_data = $smarty->fetch('mail/moderate_validation_mail_subject.tpl');
					$mail->setSubject($mail_data);
					if (!$mail->send(array($sender_email)))
						$smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
				}
			} else {
				$mail_data = $smarty->fetch('mail/user_validation_mail.tpl');
				$mail = new TikiMail();
				$mail->setText($mail_data);
				$mail_data = $smarty->fetch('mail/user_validation_mail_subject.tpl');
				$mail->setSubject($mail_data);
				if (!$mail->send(array($_REQUEST["email"])))
					$smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
				else
					$smarty->assign('msg',$smarty->fetch('mail/user_validation_msg.tpl'));
			}
			$smarty->assign('showmsg','y');
		} else {
			$userlib->add_user($_REQUEST["name"],$_REQUEST["pass"],$_REQUEST["email"],'');
			if (isset($_REQUEST['chosenGroup']) && $userlib->get_registrationChoice($_REQUEST['chosenGroup']) == 'y') {
				$userlib->set_default_group($_REQUEST['name'], $_REQUEST['chosenGroup']);
			}			
			$logslib->add_log('register','created account '.$_REQUEST["name"]);
			$smarty->assign('msg',$smarty->fetch('mail/user_welcome_msg.tpl'));
			$smarty->assign('showmsg','y');
		}

		// save default user preferences
		$tikilib->set_user_preference($_REQUEST["name"], 'theme', $style);
		$tikilib->set_user_preference($_REQUEST["name"], 'userbreadCrumb', 4);
		$tikilib->set_user_preference($_REQUEST["name"], 'language', $language);
		$tikilib->set_user_preference($_REQUEST["name"], 'display_timezone', 'Local');
		$tikilib->set_user_preference($_REQUEST["name"], 'user_information', 'private');
		$tikilib->set_user_preference($_REQUEST["name"], 'user_dbl', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'diff_versions', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'show_mouseover_user_info', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'email is public', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'mailCharset', 'utf-8');
		$tikilib->set_user_preference($_REQUEST["name"], 'realName', '');
		$tikilib->set_user_preference($_REQUEST["name"], 'homePage', '');
		$tikilib->set_user_preference($_REQUEST["name"], 'lat', floatval(0));
		$tikilib->set_user_preference($_REQUEST["name"], 'lon', floatval(0));
		$tikilib->set_user_preference($_REQUEST["name"], 'country', '');
		$tikilib->set_user_preference($_REQUEST["name"], 'mess_maxRecords', 10);
		$tikilib->set_user_preference($_REQUEST["name"], 'mess_archiveAfter', 0);
		$tikilib->set_user_preference($_REQUEST["name"], 'mess_sendReadStatus', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'minPrio', 6);
		$tikilib->set_user_preference($_REQUEST["name"], 'allowMsgs', (($allowmsg_by_default == 'y')?'y':'n'));
		$tikilib->set_user_preference($_REQUEST["name"], 'mytiki_pages', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'mytiki_blogs', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'mytiki_gals', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'mytiki_msgs', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'mytiki_tasks', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'mytiki_items', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'mytiki_workflow', 'n');
		$tikilib->set_user_preference($_REQUEST["name"], 'tasks_maxRecords', 10);

		// Custom fields
		foreach ($customfields as $custpref=>$prefvalue ) {
		    //print $_REQUEST[$customfields[$custpref]['prefName']];
		    if (isset($_REQUEST[$customfields[$custpref]['prefName']]))
				$tikilib->set_user_preference($_REQUEST["name"], $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
		}

		$emails = $notificationlib->get_mail_events('user_registers','*');
		if (count($emails)) {
			include_once("lib/notifications/notificationemaillib.php");
			$smarty->assign('mail_user',$_REQUEST["name"]);
			$smarty->assign('mail_date',date("U"));
			$smarty->assign('mail_site',$_SERVER["SERVER_NAME"]);
			sendEmailNotification($emails, "email", "new_user_notification_subject.tpl", null, "new_user_notification.tpl");
		}

	}
}
$smarty->assign('email_valid',$email_valid);

$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
foreach ($listgroups['data'] as $gr) {
	if ($gr['registrationChoice'] == 'y') {
		$smarty->assign('listgroups', $listgroups['data'] );
		break;
	}
}
ask_ticket('register');

$_VALID = tra("Please enter a valid %s.  No spaces, more than %d characters and contain %s");

$smarty->assign('_PROMPT_UNAME', sprintf($_VALID, tra("username"), $min_user_length, "0-9,a-z,A-Z") );
$smarty->assign('_PROMPT_PASS', sprintf($_VALID, tra("password"), $min_pass_length, "0-9,a-z,A-Z") );
$smarty->assign('min_user_length', $min_user_length);
$smarty->assign('min_pass_length', $min_pass_length);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid','tiki-register.tpl');
$smarty->display("tiki.tpl");

?>
