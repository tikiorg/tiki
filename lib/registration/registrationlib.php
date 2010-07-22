<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @class RegistrationLib
 *
 * This class provides registration functions
 *
 * @date created: 2003/3/21 16:48
 */

//this script may only be included - so it's better to die if called directly
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	die;
}

require_once('lib/tikilib.php'); // httpScheme(), get_user_preference
require_once('lib/webmail/tikimaillib.php');
require_once('lib/db/tiki_registration_fields.php');
require_once('lib/notifications/notificationlib.php');

if (!isset($Debug)) $Debug = false;

class RegistrationLib extends TikiLib
{

    // Validate emails...
	function SnowCheckMail($Email, $sender_email, $novalidation, $Debug=false)
	{
		global $prefs;
		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}	
		$HTTP_HOST=$_SERVER['SERVER_NAME']; 
		$Return =array();
		// Variable for return.
		// $Return[0] : [true|false]
		// $Return[1] : Processing result save.

		//Fix by suilinma
		if (!preg_match('/^[-_a-z0-9+]+(\\.[-_a-z0-9+]+)*\\@([-a-z0-9]+\\.)*([a-z]{2,4})$/i', $Email)) {
			// luci's regex that also works
			//	if (!eregi("^[_a-z0-9\.\-]+@[_a-z0-9\.\-]+\.[a-z]{2,4}$", $Email)) {
			$Return[0]=false;
			$Return[1]="${Email} is E-Mail form that is not right.";
			if ($Debug) echo "Error : {$Email} is E-Mail form that is not right.<br>";
			return $Return;
		}
		else if ($Debug) echo "Confirmation : {$Email} is E-Mail form that is right.<br>";

		// E-Mail @ by 2 by standard divide. if it is $Email this "lsm@ebeecomm.com"..
		// $Username : lsm
		// $Domain : ebeecomm.com
		// list function reference : http://www.php.net/manual/en/function.list.php
		// split function reference : http://www.php.net/manual/en/function.split.php
		list ($Username, $Domain) = explode ("@", $Email);
	
		if($prefs['validateEmail'] == 'n') {
			$Return[0]=true;
			$Return[1]="The email appears to be correct."; 
			Return $Return;
		}

		// That MX(mail exchanger) record exists in domain check .
		// checkdnsrr function reference : http://www.php.net/manual/en/function.checkdnsrr.php
		if ( checkdnsrr ( $Domain, "MX" ) )  {
			if($Debug) echo "Confirmation : MX record about {$Domain} exists.<br>";
			// If MX record exists, save MX record address.
			// getmxrr function reference : http://www.php.net/manual/en/function.getmxrr.php
			if ( getmxrr ($Domain, $MXHost))  {
				if($Debug) {
					echo "Confirmation : Is confirming address by MX LOOKUP.<br>";
					$j=0;
					foreach($MXHost as $mxh) {
						echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result(".++$j.") - $mxh<BR>";
					}
				}
			}
			// Getmxrr function does to store MX record address about $Domain in arrangement form to $MXHost.
			// $ConnectAddress socket connection address.
			$ConnectAddress = $MXHost[0];
		} else {
			// If there is no MX record simply @ to next time address socket connection do .
			$ConnectAddress = $Domain;
			if ($Debug) echo "Confirmation : MX record about {$Domain} does not exist.<br>";
			if ($novalidation == 'mini') {
				$Return[0]=false;
				$Return[1]="{$Email} domain is incorrect.";
				return $Return;
			}
		}

		if ($novalidation != 'yes' && $novalidation != 'mini') {	// Skip the connecting test if it didn't work the first time
			// fsockopen function reference : http://www.php.net/manual/en/function.fsockopen.php
			@$Connect = fsockopen ( $ConnectAddress, 25 );

			// Success in socket connection
			if ($Connect) {
				if ($Debug) 
					echo "Connection succeeded to {$ConnectAddress} SMTP.<br>";
				// Judgment is that service is preparing though begin by 220 getting string after connection .
				// fgets function reference : http://www.php.net/manual/en/function.fgets.php
				if ( preg_match('/^220/', $Out = fgets ($Connect, 1024 ))) {
					// Inform client's reaching to server who connect.
					fputs ( $Connect, "HELO $HTTP_HOST\r\n" );
					if ($Debug) echo "Run : HELO $HTTP_HOST<br>";
					$Out = fgets ( $Connect, 1024 ); // Receive server's answering cord.

					// Inform sender's address to server.
					fputs ( $Connect, "MAIL FROM: <{$prefs['sender_email']}>\r\n" );
					if ($Debug) echo "Run : MAIL FROM: &lt;{$prefs['sender_email']}&gt;<br>";
					$From = fgets ( $Connect, 1024 ); // Receive server's answering cord.

					// Inform listener's address to server.
					fputs ( $Connect, "RCPT TO: <{$Email}>\r\n" );
					if ($Debug) echo "Run : RCPT TO: &lt;{$Email}&gt;<br>";
					$To = fgets ( $Connect, 1024 ); // Receive server's answering cord.

					// Finish connection.
					fputs ( $Connect, "QUIT\r\n");
					if ($Debug) echo "Run : QUIT<br>";

					fclose($Connect);

					// Server's answering cord about MAIL and TO command checks.
					// Server about listener's address reacts to 550 codes if there does not exist
					// checking that mailbox is in own E-Mail account.
					if (!preg_match('/^250/', $From ) || !preg_match ( '/^250/', $To)) {
						$Return[0] = false;
						$Return[1] = 'not_recognized';
						if ($Debug) echo "{$Email} is not recognized by the mail server.<br>";
						return $Return;
					}
				}
			}
			// Failure in socket connection
			else {
				$Return[0] = false;
				$Return[1] = "Cannot connect to mail server ({$ConnectAddress}).";
				if ($Debug) echo "Cannot connect to mail server ({$ConnectAddress}).<br>";
				return $Return;
			}
		}
		$Return[0]=true;
		$Return[1]="{$Email} is valid.";
		return $Return;
	}


	function get_customfields($user=false) {
		$table = new TikiRegistrationFields();
		return $table->getVisibleFields2($user);
	}       


	/**
	 *  Create a new user in the database on user registration
	 *  @access private
	 *  @returns true on success, false to halt event proporgation
	 */
	function create_user() {
		global $_REQUEST, $_SERVER, $email_valid, $prefs
			, $registrationlib_apass, $customfields, $userlib, $tikilib, $Debug
			;

		if ($Debug) 
			print "::create_user";

		if ($email_valid != 'no') {
			if ($prefs['validateUsers'] == 'y') {
				//$apass = addslashes(substr(md5($tikilib->genPass()), 0, 25));
				$apass = addslashes(md5($tikilib->genPass()));
				$registrationlib_apass = $apass;
				$userlib->add_user($_REQUEST['name'], $apass, $_REQUEST["email"], $_REQUEST["pass"]);
			} else {
				$userlib->add_user($_REQUEST['name'], $_REQUEST["pass"], $_REQUEST["email"], '');
			}

			// Custom fields
			foreach ($customfields as $custpref=>$prefvalue ) {
				if ($customfields[$custpref]['show']) {
					//print $_REQUEST[$customfields[$custpref]['prefName']];
					$tikilib->set_user_preference($_REQUEST['name'], $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
				}
			}
		}
		return true;
	}

	/**
	 *  Check registration datas
	 *  @param $registration datas of registration (login, pass, email, etc.)
	 *  @returns ?
	 */
	/*private*/
	function local_check_registration($registration) {
		global $_SESSION, $prefs, $user, $userlib, $captchalib;

		if (empty($registration['name']))
			return new RegistrationError('name', tra("Username is required"));
		
		if (empty($registration['pass']) && !isset($_SESSION['openid_url']))
			return new RegistrationError('pass', tra("Password is required"));
		
		if ($novalidation != 'yes' and ($registration["pass"] != $registration["passAgain"]) and !isset($_SESSION['openid_url']))
			return new RegistrationError('passAgain', tra("The passwords don't match"));
		
		if ($userlib->user_exists($registration['name']))
			return new RegistrationError('name', tra("User already exists"));
		
		if (!$user && $prefs['feature_antibot'] == 'y') {
			if (!$captchalib->validate())
				return new RegistrationError('antibotcode', $captchalib->getErrors());
		}
		
		// VALIDATE NAME HERE
		$n = strtolower($registration['name']);
		if ($n == 'admin' || $n == 'anonymous' || $n == 'registered' || $n == strtolower(tra('Anonymous')) || $n == strtolower(tra('Registered')))
			return new RegistrationError('name', tra("Invalid username"));
		
		if (strlen($registration['name']) > 200)
			return new RegistrationError('name', tra("Username is too long"));
		
		if ($prefs['lowercase_username'] == 'y') {
			if (preg_match('/[[:upper:]]/', $registration['name']))
			return new RegistrationError('name', tra("Username cannot contain uppercase letters"));
		}

		if (strlen($registration['name']) < $prefs['min_username_length'])
			return new RegistrationError('name', tra("Username must be at least") . ' ' . $prefs['min_username_length'] . ' ' . tra("characters long"));
			
		if (strlen($registration['name']) > $prefs['max_username_length'])
			return new RegistrationError('name', tra("Username cannot contain more than") . ' ' . $prefs['max_username_length'] . ' ' . tra("characters"));
		
		$newPass = $registration['pass'] ? $registration['pass'] : $registration["genepass"];
		$polerr = $userlib->check_password_policy($newPass);
		if (!isset($_SESSION['openid_url']) && (strlen($polerr) > 0))
			return new RegistrationError('pass', $polerr);
		
		if (!empty($prefs['username_pattern']) && !preg_match($prefs['username_pattern'], $registration['name']))
			return new RegistrationError('name', tra("Invalid username"));
		
		// Check the mode
		if ($prefs['useRegisterPasscode'] == 'y') {
			if ($registration['passcode'] != $prefs['registerPasscode'])
				return new RegistrationError('passcode', tra("Wrong passcode. You need to know the passcode to register at this site"));
		}
		
		if ($nbChoiceGroups > 0 && $mandatoryChoiceGroups && empty($registration['chosenGroup']))
			return new RegistrationError('chosenGroup', tra('You must choose a group'));
		
		$email_valid = 'y';
		if (!validate_email($registration['email'], $prefs['validateEmail']))
			return new RegistrationError('email', 'email_not_valid');

		return null;
	}


	/*private*/
	function local_register_new_user($registration) {
		global $_SESSION, $tikilib, $logslib, $userlib, $notificationlib, $prefs, $smarty;
		$result="";

		if (isset($_SESSION['openid_url'])) {
			$openid_url = $_SESSION['openid_url'];
		} else {
			$openid_url = '';
		}
		$newPass = $registration['pass'] ? $registration['pass'] : $registration["genepass"];
		if ($prefs['validateUsers'] == 'y' || (isset($prefs['validateRegistration']) && $prefs['validateRegistration'] == 'y')) {
			$apass = addslashes(md5($tikilib->genPass()));
			$userlib->send_validation_email($registration['name'], $apass, $registration['email'], '', '', isset($registration['chosenGroup']) ? $registration['chosenGroup'] : '');
			$userlib->add_user($registration['name'], $newPass, $registration["email"], '', false, $apass, $openid_url , $prefs['validateRegistration'] == 'y'?'a':'u');
			$logslib->add_log('register', 'created account ' . $registration['name']);
			$result="";
		} else {
			$userlib->add_user($registration['name'], $newPass, $registration["email"], '', false, NULL, $openid_url);
			$logslib->add_log('register', 'created account ' . $registration['name']);
			$result=$smarty->fetch('mail/user_welcome_msg.tpl');
		}
		if (isset($registration['chosenGroup']) && $userlib->get_registrationChoice($registration['chosenGroup']) == 'y') {
			$userlib->set_default_group($registration['name'], $registration['chosenGroup']);
		} elseif (empty($registration['chosenGroup'])) {
			$userlib->set_default_group($registration['name'], 'Registered'); // to have tiki-user_preferences links par default to the registration tracker
		}
		$userlib->set_email_group($registration['name'], $registration['email']);
		// save default user preferences
		$tikilib->set_user_preference($registration['name'], 'theme', $prefs['style']);
		$tikilib->set_user_preference($registration['name'], 'userbreadCrumb', $prefs['users_prefs_userbreadCrumb']);
		$tikilib->set_user_preference($registration['name'], 'language', $prefs['users_prefs_language']);
		$tikilib->set_user_preference($registration['name'], 'display_timezone', $prefs['users_prefs_display_timezone']);
		$tikilib->set_user_preference($registration['name'], 'user_information', $prefs['users_prefs_user_information']);
		$tikilib->set_user_preference($registration['name'], 'user_dbl', $prefs['users_prefs_user_dbl']);
		$tikilib->set_user_preference($registration['name'], 'diff_versions', $prefs['users_prefs_diff_versions']);
		$tikilib->set_user_preference($registration['name'], 'show_mouseover_user_info', $prefs['users_prefs_show_mouseover_user_info']);
		$tikilib->set_user_preference($registration['name'], 'email is public', $prefs['users_prefs_email_is_public']);
		$tikilib->set_user_preference($registration['name'], 'mailCharset', $prefs['users_prefs_mailCharset']);
		$tikilib->set_user_preference($registration['name'], 'realName', '');
		$tikilib->set_user_preference($registration['name'], 'homePage', '');
		$tikilib->set_user_preference($registration['name'], 'lat', floatval(0));
		$tikilib->set_user_preference($registration['name'], 'lon', floatval(0));
		$tikilib->set_user_preference($registration['name'], 'country', '');
		$tikilib->set_user_preference($registration['name'], 'mess_maxRecords', $prefs['users_prefs_mess_maxRecords']);
		$tikilib->set_user_preference($registration['name'], 'mess_archiveAfter', $prefs['users_prefs_mess_archiveAfter']);
		$tikilib->set_user_preference($registration['name'], 'mess_sendReadStatus', $prefs['users_prefs_mess_sendReadStatus']);
		$tikilib->set_user_preference($registration['name'], 'minPrio', $prefs['users_prefs_minPrio']);
		$tikilib->set_user_preference($registration['name'], 'allowMsgs', $prefs['users_prefs_allowMsgs']);
		$tikilib->set_user_preference($registration['name'], 'mytiki_pages', $prefs['users_prefs_mytiki_pages']);
		$tikilib->set_user_preference($registration['name'], 'mytiki_blogs', $prefs['users_prefs_mytiki_blogs']);
		$tikilib->set_user_preference($registration['name'], 'mytiki_articles', $prefs['users_prefs_mytiki_articles']);
		$tikilib->set_user_preference($registration['name'], 'mytiki_gals', $prefs['users_prefs_mytiki_gals']);
		$tikilib->set_user_preference($registration['name'], 'mytiki_msgs', $prefs['users_prefs_mytiki_msgs']);
		$tikilib->set_user_preference($registration['name'], 'mytiki_tasks', $prefs['users_prefs_mytiki_tasks']);
		$tikilib->set_user_preference($registration['name'], 'mytiki_items', $prefs['users_prefs_mytiki_items']);
		$tikilib->set_user_preference($registration['name'], 'tasks_maxRecords', $prefs['users_prefs_tasks_maxRecords']);

		// Custom fields
		$customfields = $this->get_customfields();
		foreach($customfields as $custpref => $prefvalue) {
			if (isset($registration[$customfields[$custpref]['prefName']]))
				$tikilib->set_user_preference($registration['name'],
											  $customfields[$custpref]['prefName'],
											  $registration[$customfields[$custpref]['prefName']]);
		}
		$emails = $notificationlib->get_mail_events('user_registers', '*');
		if (count($emails)) {
			require_once ("lib/notifications/notificationemaillib.php");
			$smarty->assign('mail_user', $registration['name']);
			$smarty->assign('mail_date', $tikilib->now);
			$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
			sendEmailNotification($emails, "email", "new_user_notification_subject.tpl", null, "new_user_notification.tpl");
		}

		return $result;
	}

	/*private*/
	function intertiki_register_new_user($registration) {
	}

	/**
	 *  Check registration datas
	 *  @param $registration datas of registration (login, pass, email, etc.)
	 *  @returns : Object RegistrationError if error
	 *             string with message if ok
	 */
	/*public*/
	function register_new_user($registration) {
		global $prefs;

		$result=$this->local_check_registration($registration);
		if ($result !== null) return $result;

		if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
			// register to main
			$result=$this->intertiki_register_new_user($registration);
		} else {
			$result=$this->local_register_new_user($registration);
		}

		return $result;
	}
}

class RegistrationError
{
	var $field;
	var $msg;

	function RegistrationError($field, $msg) {
		$this->field=$field;
		$this->msg=$msg;
	}
}

$registrationlib = new RegistrationLib;
