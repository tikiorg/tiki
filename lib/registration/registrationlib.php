<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	die;
}

require_once('lib/tikilib.php'); // httpScheme(), get_user_preference
require_once('lib/webmail/tikimaillib.php');
require_once('lib/db/tiki_registration_fields.php');

if (!isset($Debug)) {
	$Debug = false;
}

class RegistrationLib extends TikiLib
{
	public $local_prefs;
	public $master_prefs;
	public $merged_prefs;

	public function __construct()
	{
		$this->merged_prefs=$this->init_registration_prefs();
	}

	// Validate emails...
	public function SnowCheckMail($Email, $sender_email, $novalidation, $Debug=false)
	{
		global $prefs;

		if (!isset($_SERVER['SERVER_NAME'])) {
			$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'];
		}

		$HTTP_HOST=$_SERVER['SERVER_NAME'];
		$Return =array();
		// Variable for return.
		// $Return[0] : [true|false]
		// $Return[1] : Processing result save.

		if (!preg_match('/^[-_a-z0-9+]+(\\.[-_a-z0-9+]+)*\\@([-a-z0-9]+\\.)*([a-z]{2,4})$/i', $Email)) {
			$Return[0] = false;
			$Return[1] = "${Email} is E-Mail form that is not right.";

			if ($Debug) {
				echo "Error : {$Email} is E-Mail form that is not right.<br>";
			}

			return $Return;
		} elseif ($Debug) {
			echo "Confirmation : {$Email} is E-Mail form that is right.<br>";
		}

		// E-Mail @ by 2 by standard divide. if it is $Email this "lsm@ebeecomm.com"..
		// $Username : lsm
		// $Domain : ebeecomm.com
		// list function reference : http://www.php.net/manual/en/function.list.php
		// split function reference : http://www.php.net/manual/en/function.split.php
		list($Username, $Domain) = explode("@", $Email);

		if ($prefs['validateEmail'] == 'n') {
			$Return[0] = true;
			$Return[1] = 'The email appears to be correct.';
			Return $Return;
		}

		// That MX(mail exchanger) record exists in domain check .
		// checkdnsrr function reference : http://www.php.net/manual/en/function.checkdnsrr.php
		if ( checkdnsrr($Domain, 'MX') ) {
			if ($Debug) {
				echo "Confirmation : MX record about {$Domain} exists.<br>";
			}

			// If MX record exists, save MX record address.
			// getmxrr function reference : http://www.php.net/manual/en/function.getmxrr.php
			if ( getmxrr($Domain, $MXHost)) {
				if ($Debug) {
					echo 'Confirmation : Is confirming address by MX LOOKUP.<br>';
					$j = 0;

					foreach ($MXHost as $mxh) {
						echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Result(' . ++$j . ") - $mxh<BR>";
					}
				}
			}
			// Getmxrr function does to store MX record address about $Domain in arrangement form to $MXHost.
			// $ConnectAddress socket connection address.
			$ConnectAddress = $MXHost[0];
		} else {
			// If there is no MX record simply @ to next time address socket connection do .
			$ConnectAddress = $Domain;
			if ($Debug) {
				echo "Confirmation : MX record about {$Domain} does not exist.<br>";
			}
			if ($novalidation == 'mini') {
				$Return[0] = false;
				$Return[1] = "{$Email} domain is incorrect.";
				return $Return;
			}
		}

		if ($novalidation != 'yes' && $novalidation != 'mini') {
			// Skip the connecting test if it didn't work the first time
			@$Connect = fsockopen($ConnectAddress, 25);

			// Success in socket connection
			if ($Connect) {

				if ($Debug) {
					echo "Connection succeeded to {$ConnectAddress} SMTP.<br>";
				}

				// Judgment is that service is preparing though begin by 220 getting string after connection .
				// fgets function reference : http://www.php.net/manual/en/function.fgets.php
				if ( preg_match('/^220/', $Out = fgets($Connect, 1024))) {
					// Inform client's reaching to server who connect.
					fputs($Connect, "HELO $HTTP_HOST\r\n");

					if ($Debug) {
						echo "Run : HELO $HTTP_HOST<br>";
					}

					$Out = fgets($Connect, 1024); // Receive server's answering cord.

					// Inform sender's address to server.
					fputs($Connect, "MAIL FROM: <{$prefs['sender_email']}>\r\n");

					if ($Debug) {
						echo "Run : MAIL FROM: &lt;{$prefs['sender_email']}&gt;<br>";
					}

					$From = fgets($Connect, 1024); // Receive server's answering cord.

					// Inform listener's address to server.
					fputs($Connect, "RCPT TO: <{$Email}>\r\n");

					if ($Debug) {
						echo "Run : RCPT TO: &lt;{$Email}&gt;<br>";
					}

					$To = fgets($Connect, 1024); // Receive server's answering cord.

					// Finish connection.
					fputs($Connect, "QUIT\r\n");
					if ($Debug) {
						echo "Run : QUIT<br>";
					}

					fclose($Connect);

					// Server's answering cord about MAIL and TO command checks.
					// Server about listener's address reacts to 550 codes if there does not exist
					// checking that mailbox is in own E-Mail account.
					if (!preg_match('/^250/', $From) || !preg_match('/^250/', $To)) {
						$Return[0] = false;
						$Return[1] = 'not_recognized';
						if ($Debug) {
							echo "{$Email} is not recognized by the mail server.<br>";
						}
						return $Return;
					}
				}
			} else {
				// Failure in socket connection
				$Return[0] = false;
				$Return[1] = "Cannot connect to mail server ({$ConnectAddress}).";
				if ($Debug) {
					echo "Cannot connect to mail server ({$ConnectAddress}).<br>";
				}
				return $Return;
			}
		}
		$Return[0] = true;
		$Return[1] = "{$Email} is valid.";
		return $Return;
	}

	public function get_customfields($user = false)
	{
		$table = new TikiRegistrationFields();
		return $table->getVisibleFields2($user);
	}

	/**
	 *  Check registration datas
	 * @param $registration array of registration (login, pass, email, etc.)
	 * @param bool $from_intertiki
	 * @return array (of errors)
	 */
	private function local_check_registration($registration, $from_intertiki = false)
	{
		global $prefs;
		$userlib = TikiLib::lib('user');
		$captchalib = TikiLib::lib('captcha');

		$errors = array();

		//do not recheck if already validated
		if (!isset($registration['valerror']) || $registration['valerror'] !== false) {
			$validateName = 1;
			if ($prefs['login_autogenerate'] == 'y') {
				$validateName = 0;
			}
			
			if (empty($registration['name']) && $validateName) {
				$errors[] = new RegistrationError('name', tra('Username is required'));
			}

			if (empty($registration['pass']) && !isset($_SESSION['openid_url'])) {
				$errors[] = new RegistrationError('pass', tra('Password is required'));
			}

			// novalidation is set to yes if a user confirms his email is correct after tiki fails to validate it
			$novalidation=isset($_REQUEST['novalidation']) ? $registration['novalidation'] : '';
			if ($novalidation != 'yes' and ($registration['pass'] != $registration['passAgain']) and !isset($_SESSION['openid_url'])) {
				$errors[] = new RegistrationError('passAgain', tra("The passwords don't match"));
			}

			if ($userlib->user_exists($registration['name'])) {
				$errors[] = new RegistrationError('name', tra('User already exists'));
			}

			if (!$from_intertiki && $prefs['feature_antibot'] == 'y') {
				if (!$captchalib->validate($registration)) {
					$errors[] = new RegistrationError('antibotcode', $captchalib->getErrors());
				}
			}

			// VALIDATE NAME HERE
			$n = strtolower($registration['name']);
			if ($n == 'admin'
					|| $n == 'anonymous'
					|| $n == 'registered'
					|| $n == strtolower(tra('Anonymous'))
					|| $n == strtolower(tra('Registered'))
			) {
				$errors[] = new RegistrationError('name', tra('Invalid username'));
			}

			if (strlen($registration['name']) > 200 && $validateName) {
				$errors[] = new RegistrationError('name', tra('Username is too long'));
			}

			if ($this->merged_prefs['lowercase_username'] == 'y' && $validateName) {
				if (preg_match('/[[:upper:]]/', $registration['name'])) {
					$errors[] = new RegistrationError('name', tra('Username cannot contain uppercase letters'));
				}
			}

			if (strlen($registration['name']) < $this->merged_prefs['min_username_length'] && $validateName) {
				$errors[] = new RegistrationError(
					'name',
					tr("Username must be at least %0 characters long", $this->merged_prefs['min_username_length'])
				);
			}

			if (strlen($registration['name']) > $this->merged_prefs['max_username_length'] && $validateName) {
				$errors[] = new RegistrationError(
					'name',
					tr("Username cannot contain more than %0 characters", $this->merged_prefs['max_username_length'])
				);
			}

			$newPass = $registration['pass'] ? $registration['pass'] : $registration['genepass'];
			$polerr = $userlib->check_password_policy($newPass);

			if (!isset($_SESSION['openid_url']) && (strlen($polerr) > 0)) {
				$errors[] = new RegistrationError('pass', $polerr);
			}

			if (!empty($this->merged_prefs['username_pattern']) && !preg_match($this->merged_prefs['username_pattern'], $registration['name']) && $validateName) {
				$errors[] = new RegistrationError('name', tra('Invalid username'));
			}

			// Check the mode
			if ($this->local_prefs['useRegisterPasscode'] == 'y') {
				if ($registration['passcode'] != $prefs['registerPasscode']) {
					$errors[] = new RegistrationError('passcode', tra('Wrong passcode. You need to know the passcode to register at this site'));
				}
			}

			if (count($this->merged_prefs['choosable_groups']) > 0
					&& $this->merged_prefs['mandatoryChoiceGroups']
					&& (empty($registration['chosenGroup']) ||
						$userlib->get_registrationChoice($registration['chosenGroup']) !== 'y')
			) {
				$errors[] = new RegistrationError('chosenGroup', tra('You must choose a group'));
			}

			$email_valid = 'y';
			if (!validate_email($registration['email'], $this->merged_prefs['validateEmail'])) {
				$errors[] = new RegistrationError('email', tra('Email not valid. Should be in the format "mailbox@example.com".'));
			}
		}
		return $errors;
	}

	/**
	 * @param $registration
	 * @param $from_intertiki
	 * @return string
	 */
	private function register_new_user_local($registration, $from_intertiki)
	{
		global $prefs;
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');
		$logslib = TikiLib::lib('logs');
		$notificationlib = TikiLib::lib('notification');

		$result = '';

		if (isset($_SESSION['openid_url'])) {
			$openid_url = $_SESSION['openid_url'];
		} else {
			$openid_url = '';
		}

		$newPass = $registration['pass'] ? $registration['pass'] : $registration["genepass"];

		$pending = false;
		$confirmed = false;

		if ($prefs['userTracker'] === 'y') {
			// this gets called twice if there's a user tracker
			if (!$userlib->get_user_real_case($registration['name'])) {
				$pending = true;				// first time to just create the basic user record for the tracker to attach to
			} else {
				$confirmed = true;				// second time to send notifications, join groups etc
			}
		}

		if ($this->merged_prefs['validateUsers'] == 'y'
				|| (isset($this->merged_prefs['validateRegistration'])
						&& $this->merged_prefs['validateRegistration'] == 'y')
		) {
			if ($confirmed) {
				$info = $userlib->get_user_info($registration['name']);
				$apass = $info['valid'];
			} else {
				$apass = md5($tikilib->genPass());
			}

			if (!$pending) {
				// don't send validation until user tracker has been validated
				$userlib->send_validation_email(
					$registration['name'],
					$apass,
					$registration['email'],
					'',
					'',
					isset($registration['chosenGroup']) ? $registration['chosenGroup'] : 'Registered'
				);
			}

			if (!$confirmed) {
				$registration['name'] = $userlib->add_user(
					$registration['name'],
					$newPass,
					$registration["email"],
					'',
					false,
					$apass,
					$openid_url,
					$this->merged_prefs['validateRegistration'] == 'y'?'a':'u'
				); //returns the user login and set as name. this is in case the name is generated in the add user (autogenerated login for eg)
				if ($registration['name']) {
					$_REQUEST['name'] = $registration['name']; // update in case auto-generated
				}
			}

			if (!$pending) {
				$smarty->assign('username', $registration['name']);
				$logslib->add_log('register', 'created account ' . $registration['name']);
				if ($this->merged_prefs['validateRegistration'] == 'y') {
					$result=nl2br($smarty->fetch('mail/user_validation_waiting_msg.tpl'));
				} else {
					$result=$smarty->fetch('mail/user_validation_msg.tpl');
				}
			}
		} else {
			if (!$confirmed) {
				$registration['name'] = $userlib->add_user($registration['name'], $newPass, $registration['email'], '', false, null, $openid_url);
				if ($registration['name']) {
					$_REQUEST['name'] = $registration['name']; // update in case auto-generated
				}
			}
			if (!$pending) {
				$smarty->assign('username', $registration['name']);
				$logslib->add_log('register', 'created account ' . $registration['name']);
				$result=$smarty->fetch('mail/user_welcome_msg.tpl');
			}
		}

		if ($pending) {
			return '';
		}

		if (isset($registration['chosenGroup']) && $userlib->get_registrationChoice($registration['chosenGroup']) == 'y') {
			$userlib->set_default_group($registration['name'], $registration['chosenGroup']);
		} elseif (empty($registration['chosenGroup'])) {
			// to have tiki-user_preferences links par default to the registration tracker
			$userlib->set_default_group($registration['name'], 'Registered');
		}

		$userlib->set_email_group($registration['name'], $registration['email']);

		// save default user preferences
		// Custom fields
		$customfields = $this->get_customfields();

		foreach ($customfields as $custpref => $prefvalue) {
			if (isset($registration[$customfields[$custpref]['prefName']])) {
				$tikilib->set_user_preference(
					$registration['name'],
					$customfields[$custpref]['prefName'],
					$registration[$customfields[$custpref]['prefName']]
				);
			}
		}

		$watches = $tikilib->get_event_watches('user_registers', '*');

		if (count($watches)) {
			require_once ("lib/notifications/notificationemaillib.php");
			$smarty->assign('mail_user', $registration['name']);
			$smarty->assign('mail_site', $_SERVER["SERVER_NAME"]);
			sendEmailNotification($watches, null, 'new_user_notification_subject.tpl', null, 'new_user_notification.tpl');
		}

		return $result;
	}

	/**
	 * @param $registration array
	 * @param $from_intertiki bool
	 * @return mixed|RegistrationError
	 */
	private function register_new_user_to_intertiki($registration, $from_intertiki)
	{
		global $prefs;

		$remote = $prefs['interlist'][$prefs['feature_intertiki_mymaster']];
		$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
		$client->setDebug(0);

		$msg = new XML_RPC_Message(
			'intertiki.registerUser',
			array(new XML_RPC_Value($prefs['tiki_key'], 'string'),
			XML_RPC_encode($registration))
		);

		$result = $client->send($msg);

		if (!$result || $result->faultCode()) {
			return new RegistrationError('intertiki', 'Master returned an error : ' . ($result ? $result->faultString() : $result));
		}

		$result = $result->value();
		$result = XML_RPC_decode($result);
		if (array_key_exists('field', $result) && array_key_exists('msg', $result)) {
			// this is a RegistrationError
			$result = new RegistrationError($result['field'], $result['msg']);
		}
		return $result;
	}

	/*called by remote.php*/
	public function register_new_user_from_intertiki($registration)
	{
		return $this->register_new_user($registration, true);
	}

	/**
	 *  Check registration data
	 * @param $registration array of registration (login, pass, email, etc.)
	 * @param $from_intertiki bool
	 * @return array|string RegistrationError if error, string with message if ok
	 */
	public function register_new_user($registration, $from_intertiki=false)
	{
		global $prefs;
		$tikilib = TikiLib::lib('tiki');

		if ($prefs['login_is_email'] == 'y' && isset($registration['name'])) {
			$registration['email'] = $registration['name'];
		}
		//result is empty if validation (including antibot) is successful
		$result=$this->local_check_registration($registration, $from_intertiki);
		if (!empty($result)) {
			return $result;
		}
		//initialize the name as an empty string
		if (!isset($registration['name'])) {
			$registration['name'] = "";
		}

		if ($prefs['feature_invite'] == 'y') {
			unset($registration['invitedid']);
			$invite = 0;

			if (!$from_intertiki && array_key_exists('invite', $registration)) {
				$invite = (int) $registration['invite'];

				$res = $tikilib->query(
					'SELECT * FROM tiki_invited WHERE id_invite=? AND email=? AND used=?',
					array($invite, $registration['email'], 'no')
				);

				$invited=$res->fetchRow();

				if (!is_array($invited)) {
					return new RegistrationError('invite', tra("This invitation does not exist or is deprecated or wrong email"));
				} else {
					$registration['invitedid']=$invited['id'];
				}
			} else {
				unset($registration['invite']);
			}
		}
		//user account created here
		if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
			// register to main
			$result=$this->register_new_user_to_intertiki($registration, $from_intertiki);
		} else {
			$result=$this->register_new_user_local($registration, $from_intertiki);
		}

		if ($prefs['feature_invite'] == 'y') {
			if ($invite > 0) {
				$res=$tikilib->query('SELECT * FROM tiki_invite WHERE id=?', array($invite));
				$inviterow=$res->fetchRow();
				if (!is_array($inviterow)) {
					die('(bug) This invitation does not exist or is deprecated');
				}

				$tikilib->query(
					'UPDATE tiki_invited SET used=? , used_on_user=? WHERE id=?',
					array('registered', $registration['name'], $registration['invitedid'])
				);

				if (!empty($inviterow['wikipageafter'])) {
					$GLOBALS['redirect'] =
									str_replace('tiki-register.php', 'tiki-index.php?page=', $_SERVER['SCRIPT_URI']) .
										urlencode($inviterow['wikipageafter']);
				}
			}
		}

		return $result;
	}

	/**
	 * Adds jquery validation javascript to the page to validate the registration form inputs
	 */
	public function addRegistrationFormValidationJs()
	{
		global $prefs, $user;

		if ($prefs['feature_jquery_validation'] === 'y') {
			$js_m = '';
			$js = '
			$("form[name=RegForm]").validate({
				rules: {
					name: {
						required: true,';
			if ($prefs['login_is_email'] === 'y') {
				$js .= '
						email: true,';
			}
			$js .= '
						remote: {
							url: "validate-ajax.php",
							type: "post",
							data: {
								validator: "username",
								input: function() { return $("#name").val(); }
							}
						}
					},
					email: {';
			if ($prefs['user_unique_email'] == 'y') {
				$js .= '
						remote: {
							url: "validate-ajax.php",
							type: "post",
							data: {
								validator: "uniqueemail",
								input: function() { return $("#email").val(); }
							}
						},';
			}
			$js .= '
						required: true,
						email: true
					},
					pass: {
						required: true,
						remote: {
							url: "validate-ajax.php",
							type: "post",
							data: {
								validator: "password",
								input: function() { return $("#pass1").val(); }
							}
						}
					},
					passAgain: { equalTo: "#pass1" }';

			if ($prefs['user_must_choose_group'] === 'y') {
				$choosable_groups = $this->merged_prefs['choosable_groups'];
				$js .= ',
					chosenGroup: {
						required: true
					}';
				$js_m .= ' "chosenGroup": { required: "' . tra('One of these groups is required') . '"}, ';
			}

			if (extension_loaded('gd') && function_exists('imagepng') && function_exists('imageftbbox') && $prefs['feature_antibot'] == 'y' && empty($user) && $prefs['recaptcha_enabled'] != 'y') {
				// antibot validation
				$js .= ',
			"captcha[input]": {
				required: true,
				remote: {
					url: "validate-ajax.php",
					type: "post",
					data: {
						validator: "captcha",
						parameter: function() { return $("#captchaId").val(); },
						input: function() { return $("#antibotcode").val(); }
					}
				}
			}
		';
				$js_m .= ' "captcha[input]": { required: "' . tra('This field is required') . '"}, ';
			}

			$js .= '
				},
				messages: {' . $js_m . '
					name: { required: "This field is required"},
					email: { email: "Invalid email", required: "This field is required"},
					pass: { required: "This field is required"},
					passAgain: { equalTo: "Passwords do not match"}
				},
				submitHandler: function(){process_submit(this.currentForm);}
			});
		';
			TikiLib::lib('header')->add_jq_onready($js);
		}
	}

	private function init_registration_prefs()
	{
		global $prefs;
		$userlib = TikiLib::lib('user');

		if (!is_array($this->merged_prefs)) {
			// local tiki prefs
			$this->local_prefs = array(
							'feature_antibot' => $prefs['feature_antibot'],
							'lowercase_username' => $prefs['lowercase_username'],
							'min_username_length' => $prefs['min_username_length'],
							'max_username_length' => $prefs['max_username_length'],
							'min_pass_length' => $prefs['min_pass_length'],
							'username_pattern' => $prefs['username_pattern'],
							'useRegisterPasscode' => $prefs['useRegisterPasscode'],
							'validateEmail' => $prefs['validateEmail'],
							'validateUsers' => $prefs['validateUsers'],
							'validateRegistration' => $prefs['validateRegistration'],
							'userTracker' => $prefs['userTracker'],
							'user_register_prettytracker' => $prefs['user_register_prettytracker'],
							'user_register_prettytracker_tpl' => $prefs['user_register_prettytracker_tpl'],
							'http_referer_registration_check' => $prefs['http_referer_registration_check'],
			);

			// local groups
			$this->local_prefs['choosable_groups']=array();
			$listgroups = $userlib->get_groups(0, -1, 'groupName_asc', '', '', 'n');
			$this->local_prefs['mandatoryChoiceGroups'] = ($prefs['user_must_choose_group'] === 'y');

			foreach ($listgroups['data'] as $gr) {
				if ($gr['registrationChoice'] == 'y') {
					$this->local_prefs['choosable_groups'][] = $gr;
					if ($gr['groupName'] == 'Registered') {
						$this->local_prefs['mandatoryChoiceGroups'] = false;
					}
				}
			}

			if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
				$remote = $prefs['interlist'][$prefs['feature_intertiki_mymaster']];
				$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
				$client->setDebug(0);

				$msg = new XML_RPC_Message(
					'intertiki.getRegistrationPrefs',
					array(new XML_RPC_Value($prefs['tiki_key'], 'string'))
				);

				$result = $client->send($msg);

				if (!$result || $result->faultCode()) {
					return new RegistrationError('', 'Master returned an error : '.($result ? $result->faultString() : $result));
				}

				$result = $result->value();
				$result = XML_RPC_decode($result);

				if (isset($result['field']) && isset($result['msg'])) {
					// this is a RegistrationError
					$result = new RegistrationError($result['field'], $result['msg']);
				}

				$this->master_prefs = $result;

				if (is_a($result, 'RegistrationError')) {
					return $result;
				}

				// merge master and local

				$this->merged_prefs=array();
				$this->merged_prefs['feature_antibot'] = $this->local_prefs['feature_antibot']; // slave choice

				$this->merged_prefs['lowercase_username'] =
					($this->local_prefs['lowercase_username'] == 'y' || $this->master_prefs['lowercase_username'] == 'y') ? 'y' : 'n';

				$this->merged_prefs['min_username_length'] =
					($this->local_prefs['min_username_length'] > $this->master_prefs['min_username_length']) ? $this->local_prefs['min_username_length'] : $this->master_prefs['min_username_length'];

				$this->merged_prefs['max_username_length'] =
					($this->local_prefs['max_username_length'] < $this->master_prefs['max_username_length']) ? $this->local_prefs['max_username_length'] : $this->master_prefs['max_username_length'];

				$this->merged_prefs['username_pattern'] = $this->local_prefs['username_pattern']; // each will check for his prefs

				$this->merged_prefs['min_pass_length'] =
					($this->local_prefs['min_pass_length'] > $this->master_prefs['min_pass_length']) ? $this->local_prefs['min_pass_length'] : $this->master_prefs['min_pass_length'];

				if ($this->local_prefs['useRegisterPasscode'] == 'y' && $this->master_prefs['useRegisterPasscode'] == 'y') {
					return new RegistrationError('', 'Master and Slave require a passcode, only one must be set.');
				}

				if ($this->local_prefs['useRegisterPasscode'] == 'y' && $this->master_prefs['useRegisterPasscode'] == 'y') {
					return new RegistrationError('', 'Master and Slave require a passcode, only one must be set.');
				}

				$this->merged_prefs['validateEmail'] = $this->local_prefs['validateEmail'];
				$this->merged_prefs['validateUsers'] = $this->local_prefs['validateUsers'];
				$this->merged_prefs['validateRegistration'] = $this->local_prefs['validateRegistration'];
				$this->merged_prefs['userTracker'] = $this->local_prefs['userTracker'];
				$this->merged_prefs['user_register_prettytracker'] = $this->local_prefs['user_register_prettytracker'];
				$this->merged_prefs['user_register_prettytracker'] = $this->local_prefs['user_register_prettytracker'];
				$this->merged_prefs['choosable_groups'] = $this->local_prefs['choosable_groups'];
				$this->merged_prefs['mandatoryChoiceGroups'] = $this->local_prefs['mandatoryChoiceGroups'];
			} else {
				$this->merged_prefs = $this->local_prefs;
				$this->remote_prefs = $this->local_prefs;
			}
		}
		return $this->merged_prefs;
	}

}

/**
 * RegistrationError
 *
 */
class RegistrationError
{
	public $field;
	public $msg;

	public function __construct($field, $msg)
	{
		$this->field = $field;
		$this->msg = $msg;
	}
}
