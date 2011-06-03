<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

// Lib for user administration, groups and permissions
// This lib uses pear so the constructor requieres
// a pear DB object

// some definitions for helping with authentication
define("USER_VALID", 2);

define("SERVER_ERROR", -1);
define("PASSWORD_INCORRECT", -3);
define("USER_NOT_FOUND", -5);
define("ACCOUNT_DISABLED", -6);
define("ACCOUNT_WAITING_USER", -9);
define ("USER_AMBIGOUS", -7);
define('USER_NOT_VALIDATED', -8);

//added for Auth v1.3 support
define ("AUTH_LOGIN_OK", 0);

class UsersLib extends TikiLib
{
	// change this to an email address to receive debug emails from the LDAP code
	var $debug = false;

	var $usergroups_cache;
	var $groupperm_cache;
	var $groupinclude_cache;
	var $userobjectperm_cache; // used to cache queries in object_has_one_permission()
	var $get_object_permissions_for_user_cache;

	function __construct() {
		parent::__construct();

		// Initialize caches
		$this->usergroups_cache = array();
		$this->groupperm_cache = array(array());
		$this->groupinclude_cache = array();
		$this->get_object_permissions_for_user_cache = array();
	}

	function assign_object_permission($groupName, $objectId, $objectType, $permName) {
		$objectId = md5($objectType . strtolower($objectId));

		$query = "delete from `users_objectpermissions`	where `objectId` = ? and `objectType`=?";
		$bindvars = array($objectId, $objectType);
		if (!empty($groupName)) {
			$query .= ' and `groupName` = ?';
			$bindvars[] = $groupName;
		}
		if (!empty($permName)) {
			$query .= ' and `permName` = ?';
			$bindvars[] = $permName;
		}
		$result = $this->query($query, $bindvars);

		if (!empty($permName) && !empty($groupName)) {
			$query = "insert into `users_objectpermissions`(`groupName`,
			`objectId`, `objectType`, `permName`)
			values(?, ?, ?, ?)";
			$result = $this->query($query, array($groupName, $objectId,
			$objectType, $permName));
		}
		if ($objectType == 'file gallery') {
			global $cachelib; require_once('lib/cache/cachelib.php');
			$cachelib->empty_type_cache('fgals_perms_'.$objectId."_");
		}
		return true;
	}

	function object_has_permission($user, $objectId, $objectType, $permName) {
		$groups = $this->get_user_groups($user);
		$objectId = md5($objectType . strtolower($objectId));
		$mid = implode(',',array_fill(0,count($groups),'?'));
		$query = "select count(*) from `users_objectpermissions` where `groupName` in ($mid) and `objectId` = ? and `objectType` = ? and `permName` = ?";
		$bindvars = array_merge($groups, array($objectId, $objectType, $permName));
		$result = $this->getOne($query, $bindvars);
		if ($result > 0) {
			return true;
		} else {
			return false;
		}
	}

	function remove_object_permission($groupName, $objectId, $objectType, $permName) {
		$objectId = md5($objectType . strtolower($objectId));

		$query = "delete from `users_objectpermissions`
			where `groupName` = ? and `objectId` = ?
			and `objectType` = ? and `permName` = ?";
		$bindvars = array($groupName, $objectId, $objectType,
			$permName);
		$result = $this->query($query, $bindvars);
		if ($objectType == 'file gallery') {
			global $cachelib; require_once('lib/cache/cachelib.php');
			$cachelib->empty_type_cache('fgals_perms_'.$objectId."_");
		}
		return true;
	}

	function copy_object_permissions($objectId,$destinationObjectId,$objectType) {
		$objectId = md5($objectType . strtolower($objectId));

		$query = "select `permName`, `groupName`
			from `users_objectpermissions`
			where `objectId` =? and
			`objectType` = ?";
		$bindvars = array($objectId, $objectType);
		$result = $this->query($query, $bindvars);
		while($res = $result->fetchRow()) {
			$this->assign_object_permission($res["groupName"],$destinationObjectId,$objectType,$res["permName"]);
		}
		return true;
	}

	function get_object_permissions($objectId, $objectType, $group='', $perm='') {
		$objectId = md5($objectType . strtolower($objectId));

		$query = "select `groupName`, `permName`
			from `users_objectpermissions`
			where `objectId` = ? and
			`objectType` = ?";
		$bindvars = array($objectId, $objectType);
		if (!empty($group)) {
			$query .= " and `groupName`=?";
			$bindvars[] = $group;
		}
		if (!empty($perm)) {
			$query .= " and `permName`=?";
			$bindvars[] = $perm;
		}
		return $this->fetchAll($query, $bindvars);
	}

	function get_object_permissions_for_user ($objectId, $objectType, $user) {
		$params = md5($objectId . $objectType . $user);
		//Check the cache for these parameters
		if (array_key_exists($params, $this->get_object_permissions_for_user_cache)) {
			return $this->get_object_permissions_for_user_cache[$params];
		}
		$objectId = md5($objectType . strtolower($objectId));
		$bindvars = array($objectId, $objectType);
		$groups = $this->get_user_groups($user);
		$bindvars = array_merge($bindvars, $groups);
		$query = 'select `permName` from `users_objectpermissions` where `objectId` = ? and `objectType` = ? and `groupName` in ('.implode(',', array_fill(0, count($groups),'?')).')';
		$result = $this->query($query, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['permName'];
		}
		//Cache the result for this set of parameters
		$this->get_object_permissions_for_user_cache[$params] = $ret;
		return $ret;
	}

	function object_has_one_permission($objectId, $objectType) {
		$objectId = md5($objectType . strtolower($objectId));

		if(!isset($this->userobjectperm_cache) || !is_array($this->userobjectperm_cache)
			|| !isset($this->userobjectperm_cache[$objectId])) {
			// i think, we really dont need the "and `objectType`=?" because the objectId should be unique due to the md5()
			$query = "select count(*) from `users_objectpermissions` where `objectId`=? and `objectType`=?";
			$this->userobjectperm_cache[$objectId]= $this->getOne($query, array(
				$objectId,
				$objectType
				));
		}

		return $this->userobjectperm_cache[$objectId];
	}

	function user_exists($user) {
		static $rv = array();
		if (!isset($rv[$user])) {
			$query = "select count(*) from `users_users` where upper(`login`) = ?";
			$result = $this->getOne($query, array(strtoupper($user)));
			$rv[$user] = $result;
		}
		return $rv[$user];
	}
	function get_user_real_case($user) {
		$query = "select `login` from `users_users` where upper(`login`) = ?";
		return $this->getOne($query, array(strtoupper($user)) );
	}

	function group_exists($group) {
		return in_array($group, $this->list_all_groups());
	}

	/**
	 * @param string $user : username
	 * @param bool $remote : logged out remotely (so do not redirect)
	 * @param string $redir : url to redirect to. Uses home page according to prefs if empty 
	 * @return void : redirects to suitable homepage or redir param if not remote 
	 */
	function user_logout($user, $remote = false, $redir = '') {
		global $prefs, $logslib, $lslib, $user_cookie_site, $phpcas_enabled;

		$logslib->add_log('login', 'logged out');
		
		$userInfo = $this->get_user_info($user);
		$this->delete_user_cookie($userInfo['userId']);
		
		if ($remote && $prefs['feature_intertiki'] == 'y' and $prefs['feature_intertiki_sharedcookie'] == 'y' and !empty($prefs['feature_intertiki_mymaster'])) {
			include_once('XML/RPC.php');
			$remote = $prefs['interlist'][$prefs['feature_intertiki_mymaster']];
			$remote['path'] = preg_replace("/^\/?/","/",$remote['path']);
			$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
			$client->setDebug(0);
			$msg = new XML_RPC_Message(
				'intertiki.logout',
				 array(
					 new XML_RPC_Value($prefs['tiki_key'], 'string'),
					 new XML_RPC_Value($user, 'string')
				)
			);
			$client->send($msg);
			return;
		}
		
		// more local cleanup originally from tiki-logout.php
		
		// go offline in Live Support
		if ($prefs['feature_live_support'] == 'y') {
			global $access; include_once ('lib/live_support/lslib.php');
			if ($lslib->get_operator_status($user) != 'offline') {
				$lslib->set_operator_status($user, 'offline');
			}
		}
		setcookie($user_cookie_site, '', -3600, $prefs['cookie_path'], $prefs['cookie_domain']);
		/* change group home page or deactivate if no page is set */
		if (!empty($redir)) {
			$url = $redir;
		} else if (($groupHome = $this->get_group_home('Anonymous')) != '') {
			$url = (preg_match('/^(\/|https?:)/', $groupHome)) ? $groupHome : 'tiki-index.php?page=' . $groupHome;
		} else {
			$url = $prefs['site_tikiIndex'];
		}
		// RFC 2616 defines that the 'Location' HTTP headerconsists of an absolute URI
		if (!preg_match('/^https?\:/i', $url)) {
			global $url_scheme, $url_host, $url_port, $base_url;
			$url = (preg_match('#^/#', $url) ? $url_scheme . '://' . $url_host . (($url_port != '') ? ":$url_port" : '') : $base_url) . $url;
		}
		if (SID) $url.= '?' . SID;

		if ( $prefs['auth_method'] === 'cas' && $user !== 'admin' && $user !== '' && $prefs['cas_force_logout'] === 'y' ) {
			phpCAS::logoutWithRedirectServiceAndUrl($url,$url);
		}
		unset($_SESSION['cas_validation_time']);
		unset($_SESSION[$user_cookie_site]);
		session_unset();
		session_destroy();
		
		if ($prefs['auth_method'] === 'ws') {
			header('Location: '.str_replace('//', '//admin:@', $url)); // simulate a fake login to logout the user
		} else {
			header('Location: ' . $url);
		}
		return;
	}

	function genPass() {
		// AWC: enable mixed case and digits, don't return too short password
		global $prefs;

		$vocales = "AaEeIiOoUu13580";
		$consonantes = "BbCcDdFfGgHhJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz24679";
		$r = '';
		$passlen = ($prefs['min_pass_length'] > 5) ? $prefs['min_pass_length'] : 5;

		for ($i = 0; $i < $passlen; $i++) {
			if ($i % 2) {
				$r .= $vocales{rand(0, strlen($vocales) - 1)};
			} else {
				$r .= $consonantes{rand(0, strlen($consonantes) - 1)};
			}
		}

		return $r;
	}

	function generate_challenge() {
		$val = md5($this->genPass());

		return $val;
	}

	function validate_hash($user, $hash) {
		return $this->getOne(
			"select count(*) from `users_users` where binary `login` = ? and `hash`=?",
			array($user, $hash)
		);
	}

	// For each auth method, validate user in auth, if valid, verify tiki user exists and create if necessary (as configured)
	// Once complete, update_lastlogin and return result, username and login message.
	function validate_user($user, $pass, $challenge = '', $response = '', $validate_phase=false) {
		global $tikilib, $prefs, $user_ldap_attributes;

		if ($user != 'admin' && $prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
			// slave intertiki sites should never check passwords locally, just for admin
			return false;
		}

		// these will help us keep tabs of what is going on
		$userTiki = false;
		$userTikiPresent = false;
		$userLdap = false;
		$userLdapPresent = false;

		// read basic pam options
		$auth_pam = ($prefs['auth_method'] == 'pam');
		$pam_create_tiki = ($prefs['pam_create_user_tiki'] == 'y');
		$pam_skip_admin = ($prefs['pam_skip_admin'] == 'y');

		// read basic LDAP options
		$auth_ldap = ($prefs['auth_method'] == 'ldap');
		$ldap_create_tiki = ($prefs['ldap_create_user_tiki'] == 'y');
		$create_auth = ($prefs['ldap_create_user_ldap'] == 'y');
		$skip_admin = ($prefs['ldap_skip_admin'] == 'y');

		// read basic cas options
		$auth_cas = ($prefs['auth_method'] == 'cas');
		$cas_create_tiki = ($prefs['cas_create_user_tiki'] == 'y');
		$cas_skip_admin = ($prefs['cas_skip_admin'] == 'y');

		// read basic phpbb options
		$auth_phpbb = ($prefs['auth_method'] == 'phpbb');
		$phpbb_create_tiki = ($prefs['auth_phpbb_create_tiki'] == 'y');
		$phpbb_skip_admin = ($prefs['auth_phpbb_skip_admin'] == 'y');
		$phpbb_disable_tikionly = ($prefs['auth_phpbb_disable_tikionly'] == 'y');

		// see if we are to use Shibboleth
		$auth_shib = ($prefs['auth_method'] == 'shib');
		$shib_create_tiki = ($prefs['shib_create_user_tiki'] == 'y');
		$shib_skip_admin = ($prefs['shib_skip_admin'] == 'y');

		// first attempt a login via the standard Tiki system
		//
		if (!($auth_shib || $auth_cas) || $user == 'admin') { //redflo: does this mean, that users in cas and shib are not replicated to tiki tables? Does this work well?
			list($result, $user) = $this->validate_user_tiki($user, $pass, $challenge, $response, $validate_phase);
		} else {
			$result = NULL;
		}
		switch ($result) {
		case USER_VALID:
			$userTiki = true;

			$userTikiPresent = true;
			break;

		case PASSWORD_INCORRECT:
			$userTikiPresent = true;

			break;
		}

		// if we aren't using LDAP this will be quick
		// if we are using tiki auth or if we're using an alternative auth except for admin
		if ((!$auth_ldap && !$auth_pam && !$auth_cas && !$auth_shib && !$auth_phpbb) || ((($auth_ldap && $skip_admin) || ($auth_shib && $shib_skip_admin) || ($auth_pam && $pam_skip_admin) || ($auth_cas && $cas_skip_admin) || ($auth_phpbb && $phpbb_skip_admin)) && $user == "admin") || ($auth_ldap && ($prefs['auth_ldap_permit_tiki_users']=='y' && $userTiki))) { // todo: bad hack. better search for a more general solution here
			// if the user verified ok, log them in
			if ($userTiki) //user validated in tiki, update lastlogin and be done
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
			// if the user password was incorrect but the account was there, give an error
			elseif ($userTikiPresent) //user ixists in tiki but bad password
				return array(false, $user, $result);
			// if the user was not found, give an error
			// this could be for future uses
			else
				return array(false, $user, $result);
		}

		// For the alternate auth methods, attempt to validate user
		// return back one of two conditions
		// Valid User or Bad password
		// next see if we need to check PAM
		elseif ($auth_pam) {

			$result = $this->validate_user_pam($user, $pass);
			switch ($result) {
			case USER_VALID:
				$userPAM = true;

				break;
			case PASSWORD_INCORRECT:
				$userPAM = false;

				break;
			}

			// start off easy
			// if the user verified in Tiki and PAM, log in
			if ($userPAM && $userTiki) {
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
			}
			// if the user wasn't found in either system, just fail
			elseif (!$userTikiPresent && !$userPAM) {
				return array(false, $user, $result);
			}
			// if the user was logged into PAM but not found in Tiki
			elseif ($userPAM && !$userTikiPresent) {
				// see if we can create a new account
				if ($pam_create_tiki) {
					// need to make this better! *********************************************************
					$result = $this->add_user($user, $pass, '');

					// if it worked ok, just log in
					if ($result == USER_VALID)
						// before we log in, update the login counter
						return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
					// if the server didn't work, do something!
					elseif ($result == SERVER_ERROR) {
						// check the notification status for this type of error
						return array(false, $user, $result);
					}
					// otherwise don't log in.
					else
						return array(false, $user, $result);
				}
				// otherwise
				else
					// just say no!
					return array(false, $user, $result);
			}
			// if the user was logged into PAM and found in Tiki (no password in Tiki user table necessary)
			elseif ($userPAM && $userTikiPresent)
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
		}

		// next see if we need to check CAS
		elseif ($auth_cas) {
			$result = $this->validate_user_cas($user);
			switch ($result) {
			case USER_VALID:
				$userCAS = true;

				break;
			case PASSWORD_INCORRECT:
				$userCAS = false;

				break;
			}

			if ($this->user_exists($user)) {
				$userTikiPresent = true;
			} else {
				$userTikiPresent = false;
			}

			// start off easy
			// if the user verified in Tiki and by CAS, log in
			if ($userCAS && $userTikiPresent) {
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
			}
			// if the user wasn't authenticated through CAS, just fail
			elseif (!$userCAS) {
				return array(false, $user, $result);
			}
			// if the user was authenticated by CAS but not found in Tiki
			elseif ($userCAS && !$userTikiPresent) {
				// see if we can create a new account
				if ($cas_create_tiki) {
					// need to make this better! *********************************************************
					$randompass = $this->genPass();
					// in case CAS auth is turned off accidentally;
					// we don't want ppl to be able to login as any user with blank passwords
					$result = $this->add_user($user, $randompass, '');

					// if it worked ok, just log in
					if ($result == USER_VALID)
						// before we log in, update the login counter
						return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
					// if the server didn't work, do something!
					elseif ($result == SERVER_ERROR) {
						// check the notification status for this type of error
						return array(false, $user, $result);
					}
					// otherwise don't log in.
					else
						return array(false, $user, $result);
				}
				// otherwise
				else
					// just say no!
					return array(false, $user, $result);
			}
			// if the user was authenticated by CAS and found in Tiki (no password in Tiki user table necessary)
			elseif ($userCAS && $userTikiPresent)
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
		}

		// next see if we need to check Shibboleth
		elseif ($auth_shib) {
			if ($this->user_exists($user)) {
				$userTikiPresent = true;
			} else {
				$userTikiPresent = false;
			}

			// Shibboleth login was not successful
			if (!isset($_SERVER['HTTP_SHIB_IDENTITY_PROVIDER'])){
				return false;
			}

			// Collect the shibboleth related attributes.
			$shibmail = $_SERVER['HTTP_MAIL'];
			$shibaffiliation = $_SERVER['HTTP_SHIB_EP_UNSCOPEDAFFILIATION'];
			$shibproviderid = $_SERVER['HTTP_SHIB_IDENTITY_PROVIDER'];

			// Get the affiliation information to log in
			$shibaffiliarray = preg_split('/;/',strtoupper($shibaffiliation));
			$validaffiliarray = preg_split('/,/',strtoupper($prefs['shib_affiliation']));
			$validafil=false;
			foreach($shibaffiliarray as $affil){
				if(in_array($affil, $validaffiliarray)){
					$validafil=true;
				}
			}

			// start off easy
			// if the user verified in Tiki and by Shibboleth, log in
			if ($userTikiPresent && $validafil) {
				return array($this->sync_and_update_lastlogin($user, $pass), $user, USER_VALID);
			}
			else {
				global $smarty;
				// see if we can create a new account
				if ($shib_create_tiki) {

					if(!(strlen($user) > 0 AND strlen($shibmail) > 0 AND strlen($shibaffiliation) > 0))
					{
						$errmsg = "User registration error: You do not have the required shibboleth attributes (";

						if (strlen($user) == 0){
							$errmsg = $errmsg . "User ";
						}

						if (strlen($shibmail) == 0){
							$errmsg = $errmsg . "Mail ";
						}

						if (strlen($shibaffiliation) == 0){
							$errmsg = $errmsg . "Affiliation ";
						}

						$errmsg = $errmsg . "). For further information on this error goto the ((ShibReg)) Page";

						$smarty->assign('msg',$errmsg );
						$smarty->display('error.tpl');
						exit;
					}
					else
					{

						if($validafil)
						{

							// Create the user
							// need to make this better! *********************************************************
							$randompass = $this->genPass();
							// in case Shibboleth auth is turned off accidentally;
							// we don't want ppl to be able to login as any user with blank passwords

							$result = $this->add_user($user, $randompass, $shibmail);

							// if it worked ok, just log in
							if ($result == USER_VALID){
								// Add to the default Group
								if ($prefs['shib_usegroup'] == 'y'){
									$result = $this->assign_user_to_group($user, $prefs['shib_group']);
								}

								// before we log in, update the login counter
								return array($this->sync_and_update_lastlogin($user, $randompass), $user, $result);
							}
							// if the server didn't work, do something!
							elseif ($result == SERVER_ERROR) {
								// check the notification status for this type of error
								return array(false, $user, $result);
							}
							// otherwise don't log in.
							else{
								return array(false, $user, $result);
							}
						}
						else
						{
							foreach($validaffiliarray as $vaffil){
								$vaffils = $vaffils . $vaffil . ", ";
							}
							$vaffils = rtrim($vaffils,", ");
							$errmsg = "<H1 align=center>User login error</H1><BR/><BR/>You must have one of the following affiliations to get into this wiki.<BR/><BR/><B>" . $vaffils . "</B><BR><BR/><BR/>For further information on this error goto the <a href=./tiki-index.php?page=ShibReg>Shibreg</a> Page";
							$smarty->assign('msg',$errmsg );
							$smarty->display('error.tpl');
							exit;
						}
					}
				}
				else{
					$smarty->assign('msg',"The user [ " . $user . " ] is not registered with this wiki.");
					$smarty->display('error.tpl');
					exit;
				}

			}

		}

		// next see if we need to check LDAP
		else if($auth_ldap){
			// check the user account
			$result = $this->validate_user_ldap($user, $pass);

			switch ($result) {
			case USER_VALID:
				$userLdap = true;

				$userLdapPresent = true;
				break;

			case PASSWORD_INCORRECT:
				$userLdapPresent = true;

				break;
			}

			// start off easy
			// if the user is in Tiki and password is verified in LDAP, log in
			if ($userLdap && $userTikiPresent) {
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
			}
			// if the user wasn't found in either system, just fail
			elseif (!$userTikiPresent && !$userLdapPresent) {
				return array(false, $user, $result);
			}
			// if the user was logged into Tiki but not found in LDAP
			elseif ($userTiki && !$userLdapPresent) {
				// see if we can create a new account
				if ($create_auth) {
					// need to make this better! *********************************************************
					$result = $this->create_user_ldap($user, $pass);

					// if it worked ok, just log in
					if ($result == USER_VALID)
						// before we log in, update the login counter
						return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
					// if the server didn't work, do something!
					elseif ($result == SERVER_ERROR) {
						// check the notification status for this type of error
						return array(false, $user, $result);
					}
					// otherwise don't log in.
					else
						return array(false, $user, $result);
				}
				// otherwise
				else
					// just say no!
					return array(false, $user, $result);
			}

			// if the user was logged into Auth but not found in Tiki
			elseif ($userLdap && !$userTikiPresent) {
				// see if we are allowed to create a new account
				if ($ldap_create_tiki) {
					// need to make this better! *********************************************************
					//$userinfo = $this->get_ldap_user_info($user,$pass);
					$result = $this->add_user($user, $pass, '');
					$this->disable_tiki_auth($user); //disable password in tiki - use ldap


					// if it worked ok, just log in
					if ($result == USER_VALID) {
						// before we log in, update the login counter
						return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
					}
					// if the server didn't work, do something!
					elseif ($result == SERVER_ERROR) {
						// check the notification status for this type of error
						return array(false, $user, $result);
					}
					// otherwise don't log in.
					else
						return array(false, $user, $result);
				}
				// otherwise
				else
					// just say no!
					return array(false, $user, $result);
			}
			// if the user was logged into Auth and found in Tiki (no password in Tiki user table necessary)
			elseif ($userLdap && $userTikiPresent)
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
		}

		elseif ($auth_phpbb) {
			$result = $this->validate_user_phpbb($user, $pass);
			switch ($result) {
			case USER_VALID:
				$userPhpbb = true;
				break;
			case PASSWORD_INCORRECT:
				$userPhpbb = false;
				break;
			}

			// start off easy
			// if the user verified in Tiki and phpBB, log in
			if ($userPhpbb && $userTiki) {
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
			}
			// if the user wasn't found in either system, just fail
			elseif (!$userTikiPresent && !$userPhpbb) {
				return array(false, $user, USER_UNKNOWN);
			}
			// if the user was logged into phpBB but not found in Tiki
			elseif ($userPhpbb && !$userTikiPresent) {
				// see if we can create a new account
				if ($phpbb_create_tiki) {
					// get the user email and then add the user to Tiki
					$the_email = $this->phpbbauth->grabEmail($user);
					$result = $this->add_user($user, $pass, $the_email);

					// if it worked ok, just log in
					if ($result == USER_VALID)
						// before we log in, update the login counter
						return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
					// if the server didn't work, do something!
					elseif ($result == SERVER_ERROR) {
						// check the notification status for this type of error
						return array(false, $user, $result);
					}
					// otherwise don't log in.
					else
						return array(false, $user, $result);
				}
				// otherwise
				else
					// just say no!
					return array(false, $user, $result);
			}
			// if the user was found in Tiki, but not found in phpBB, we should probably disable the user
			elseif ($userTikiPresent && !$userPhpbb) {
				if($phpbb_disable_tikionly) {
					// would probably be better do flag the user as not active? How do you do that?
					// and it also would be better to check if the user is active first.. :)
					$this->invalidate_account($user);
					global $logslib;
					$logslib->add_log('auth_phpbb','NOTICE: Invalidated user ' . $user . ' due to missing phpBB account.');
				}
				return array(false, $user, ACCOUNT_DISABLED);
			}
			// if the user was logged into phpBB and found in Tiki (no password in Tiki user table necessary)
			elseif ($userPhpbb && $userTikiPresent)
				return array($this->sync_and_update_lastlogin($user, $pass), $user, $result);
		}

		// we will never get here
		return array(false, $user, $result);
	}

	// validate the user through PAM
	function validate_user_pam($user, $pass) {
		global $tikilib, $prefs;

		// just make sure we're supposed to be here
		if ($prefs['auth_method'] != "pam")
			return false;

		// Read page AuthPAM at tw.o, it says about a php module required.
		// maybe and if extension line could be added here... module requires $error
		// as reference.
		if (pam_auth($user, $pass, $error)) {
			return USER_VALID;
		} else {
			// Uncomment the following to see errors on that
			// error_log("TIKI ERROR PAM: $error User: $user Pass: $pass");
			return PASSWORD_INCORRECT;
		}
	}

	function check_cas_authentication($user_cookie_site) {
		global $tikilib, $prefs, $base_url, $webdav_access;

		// Avoid CAS authentication check if the client is not able to handle HTTP redirects to another domain. This includes:
		//  - WebDAV requests
		//  - Javascript/AJAX requests
		//
		if ( ( isset($webdav_access) && $webdav_access === true )
			|| ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' )
		) {
			return true;
		}

		// just make sure we're supposed to be here
		if ( !$this->_init_cas_client() ) {
			return false;
		}

		if ( !empty($_SESSION['phpCAS']['user']) ) {
			$_SESSION[$user_cookie_site] = $_SESSION['phpCAS']['user'];
		}

		if (isset($_REQUEST['ticket']) && empty($_SESSION[$user_cookie_site])) {
			$cas_user = '';
			$_SESSION['cas_is_validating'] = false;
			$this->validate_user_cas($cas_user, true);
			die();
		}

		// Check for CAS (re-)validation
		//  Only if :
		//   - using CAS auth method
		//   - not calling tiki-login.php nor tiki-logout.php
		//   - not using 'admin' user
		//   - the request is not a POST ( which does not keep its params with CAS redirections )
		//   - either the CAS validation timed out or the validation process has not ended within 5 seconds which often means that the redirection to the CAS server failed
		//
		if (php_sapi_name() !== 'cli'
			&& (isset($_SESSION[$user_cookie_site]) || $prefs['cas_autologin'] == 'y')
			&& basename($_SERVER["SCRIPT_NAME"]) != 'tiki-login.php'
			&& basename($_SERVER["SCRIPT_NAME"]) != 'tiki-logout.php'
			&& (!isset($_SESSION[$user_cookie_site]) || $_SESSION[$user_cookie_site] != 'admin' )
			&& empty($_POST)
			&& ( ( $prefs['cas_authentication_timeout'] && $tikilib->now - $_SESSION['cas_validation_time'] > $prefs['cas_authentication_timeout'] )
				|| ( isset($_SESSION['cas_is_validating']) && $_SESSION['cas_is_validating'] === true && $tikilib->now - $_SESSION['cas_validation_time'] > 5 ) )
		) {
			unset($_SESSION["$user_cookie_site"]);
			unset($_SESSION['phpCAS']['user']);

			$_SESSION['cas_validation_time'] = $tikilib->now;
			$_SESSION['cas_is_validating'] = true;
			$cas_user = '';

			// phpCAS will always redirect to CAS validate URL
			$this->validate_user_cas($cas_user, true);

			die();
		}
	}

	function _init_cas_client() {
		global $prefs;

		// just make sure we're supposed to be here
		if ($prefs['auth_method'] != 'cas') {
			return false;
		}

		// import phpCAS lib
		require_once('lib/phpcas/CAS.php');

		// initialize phpCAS
		if ( !isset($GLOBALS['PHPCAS_CLIENT']) ) {
			phpCAS::client($prefs['cas_version'], ''.$prefs['cas_hostname'], (int) $prefs['cas_port'], ''.$prefs['cas_path'], false);
		}

		return true;
	}

	// validate the user through CAS
        function validate_user_cas(&$user, $checkOnly = false) {
		global $tikilib, $prefs, $base_url;

		// just make sure we're supposed to be here
		if (!$this->_init_cas_client()) {
			return false;
		}

		// Redirect to this URL after authentication
		if ( !empty($prefs['cas_extra_param']) && basename($_SERVER["SCRIPT_NAME"]) == 'tiki-login.php' ) {
			phpCAS::setFixedServiceURL( $base_url . 'tiki-login.php?cas=y&' . $prefs['cas_extra_param'] );
		}

		// check CAS authentication
		phpCAS::setNoCasServerValidation();
		if ( $checkOnly ) {
			unset($_SESSION['phpCAS']['auth_checked']);
			$auth = phpCAS::checkAuthentication();
		} else {
			$auth = phpCAS::forceAuthentication();
		}
		$_SESSION['cas_validation_time'] = $tikilib->now;

		// at this step, the user has been authenticated by the CAS server
		// and the user's login name can be read with phpCAS::getUser().
		if ( $auth && ($user = phpCAS::getUser()) ) {
			return USER_VALID;
		} else {
			$user = null;
			return PASSWORD_INCORRECT;
		}

	}

	function init_ldap($user, $pass) {
		global $prefs;
		if ( !isset($this->ldap) ) {
			require_once('auth/ldap.php');
			$ldap_options=array('host' => $prefs['auth_ldap_host'],
					'port' => $prefs['auth_ldap_port'],
					'version' => $prefs['auth_ldap_version'],
					'starttls' => $prefs['auth_ldap_starttls'],
					'ssl' => $prefs['auth_ldap_ssl'],
					'basedn' => $prefs['auth_ldap_basedn'],
					'scope' => $prefs['auth_ldap_scope'],
					'bind_type' => $prefs['auth_ldap_type'],
					'username' => $user,
					'password' => $pass,
					'userdn' => $prefs['auth_ldap_userdn'],
					'useroc' => $prefs['auth_ldap_useroc'],
					'userattr' => $prefs['auth_ldap_userattr'],
					'fullnameattr' => $prefs['auth_ldap_nameattr'],
					'emailattr' => $prefs['auth_ldap_emailattr'],
					'countryattr' => $prefs['auth_ldap_countryattr'],
					'groupdn' => $prefs['auth_ldap_groupdn'],
					'groupattr' => $prefs['auth_ldap_groupattr'],
					'groupoc' => $prefs['auth_ldap_groupoc'],
					'groupnameattr' => $prefs['auth_ldap_groupnameatr'],
					'groupdescattr' => $prefs['auth_ldap_groupdescatr'],
					'groupmemberattr' => $prefs['auth_ldap_memberattr'],
					'groupmemberisdn' => $prefs['auth_ldap_memberisdn'],
					'usergroupattr' => $prefs['auth_ldap_usergroupattr'],
					'groupgroupattr' => $prefs['auth_ldap_groupgroupattr'],
					'debug' => $prefs['auth_ldap_debug']
			);
			// print_r($ldap_options);
			$this->ldap=new TikiLdapLib($ldap_options);
		}
	}

	// validate the user via ldap and get a ldap connection
	function validate_user_ldap($user, $pass) {
		if (!$pass) { // An LDAP password cannot be empty. Treat specially so that Tiki does *NOT* unintentionally request an unauthenticated bind.
			return PASSWORD_INCORRECT;
		}
		
		global $prefs;
		global $logslib;

		// First connection on the ldap server in anonymous, now we can search the real name of the $user
		// It's required to pass in param the username & password because the username is used to determine the realname (dn)
		$this->init_ldap($user, $pass);

		$err = $this->ldap->bind();
		if (is_int($err)) {
			$err=Net_LDAP2::errorMessage($err);
		}
		
		// Change the default bind_type to use the full, call get_user_attributes function to use the realname (dn) in the credentials test 
		$this->ldap->setOption('bind_type', 'full');
		$this->ldap->get_user_attributes();

		// Credentials test! To test it we force the reconnection.
		$err = $this->ldap->bind(true);
		if (is_int($err)) {
				$err=Net_LDAP2::errorMessage($err);
		}
		
		switch($err) {
		case 'LDAP_INVALID_CREDENTIALS':
			return PASSWORD_INCORRECT;
		case 'LDAP_INVALID_SYNTAX':
		case 'LDAP_NO_SUCH_OBJECT':
		case 'LDAP_INVALID_DN_SYNTAX':
			if($prefs['auth_ldap_debug']=='y') $logslib->add_log('ldap','Error'.$err);
			return USER_NOT_FOUND;
		case 'LDAP_SUCCESS':
			if($prefs['auth_ldap_debug']=='y') $logslib->add_log('ldap','Bind successful.');
			return USER_VALID;
		default:
			if($prefs['auth_ldap_debug']=='y') $logslib->add_log('ldap','Error'.$err);
			return SERVER_ERROR;
		}

		// this should never happen
		die('Assertion failed '.__FILE__.':'.__LINE__);
	}

	// validate the user from a phpBB database
	function validate_user_phpbb($user, $pass) {
		require_once('auth/phpbb.php');
		$this->phpbbauth = new TikiPhpBBLib();
		switch($this->phpbbauth->check($user, $pass)) {
		case PHPBB_INVALID_CREDENTIALS:
			return PASSWORD_INCORRECT;
			break;
		case PHPBB_INVALID_SYNTAX:
		case PHPBB_NO_SUCH_USER:
			return USER_NOT_FOUND;
			break;
		case PHPBB_SUCCESS:
			//$logslib->add_log('phpbb','PhpBB user validation successful.');
			return USER_VALID;
			break;
		default:
			return SERVER_ERROR;
		}
		// this should never happen
		die('Assertion failed '.__FILE__.':'.__LINE__);
	}

	// Help function to disable the user password. Used, whenever the user password 
	// shall not be hold in the tiki db but in LDAP or somewhere else
	function disable_tiki_auth($user) {
		global $tiki;
		$query = "update `users_users` set `password`=?, `hash`=? where binary `login` = ?";
		$result = $this->query($query, array('','',$user));
	}

	// synchronize all users with ldap directory
	function ldap_sync_all_users() {
		global $prefs;
		global $logslib;

		if ($prefs['syncUsersWithDirectory'] != 'y') {
			return false;
		}

		require_once('auth/ldap.php');
		if($prefs['auth_ldap_debug']=='y') $logslib->add_log('ldap','Syncing all users with ldap');
		$bind_type = 'default';

		switch ($prefs['auth_ldap_type']) {
			// Must be anonymous or admin
			case 'default':
				break;
			default:
				if (!empty($prefs['auth_ldap_adminuser'])) {
					$bind_type = 'explicit';
					break;
				}

				return false;
		}

		$ldap_options = array(	'host' => $prefs['auth_ldap_host'],
					'port' => $prefs['auth_ldap_port'],
					'version' => $prefs['auth_ldap_version'],
					'starttls' => $prefs['auth_ldap_starttls'],
					'ssl' => $prefs['auth_ldap_ssl'],
					'basedn' => $prefs['auth_ldap_basedn'],
					'scope' => $prefs['auth_ldap_scope'],
					'bind_type' => $bind_type,
					'binddn' => $prefs['auth_ldap_adminuser'],
					'bindpw' => $prefs['auth_ldap_adminpass'],
					'userdn' => $prefs['auth_ldap_userdn'],
					'useroc' => $prefs['auth_ldap_useroc'],
					'userattr' => $prefs['auth_ldap_userattr'],
					'fullnameattr' => $prefs['auth_ldap_nameattr'],
					'emailattr' => $prefs['auth_ldap_emailattr'],
					'countryattr' => $prefs['auth_ldap_countryattr'],
					'groupdn' => $prefs['auth_ldap_groupdn'],
					'groupattr' => $prefs['auth_ldap_groupattr'],
					'groupoc' => $prefs['auth_ldap_groupoc'],
					'groupnameattr' => $prefs['auth_ldap_groupnameatr'],
					'groupdescattr' => $prefs['auth_ldap_groupdescatr'],
					'groupmemberattr' => $prefs['auth_ldap_memberattr'],
					'groupmemberisdn' => $prefs['auth_ldap_memberisdn'],
					'usergroupattr' => $prefs['auth_ldap_usergroupattr'],
					'groupgroupattr' => $prefs['auth_ldap_groupgroupattr'],
					'debug' => $prefs['auth_ldap_debug']
		);

		$user_ldap = new TikiLdapLib($ldap_options);

		if (!($users_attributes = $user_ldap->get_all_users_attributes())) {
			return false;
		}

		foreach ($users_attributes as $user_attributes) {
			$user = $user_attributes[$prefs['auth_ldap_userattr']];
			$this->add_user($user, '', $user);

			if ($prefs['auth_method'] == 'ldap') {
				$this->disable_tiki_auth($user);
			}

			$this->ldap_sync_user_data($user, $user_attributes);
		}
	}

	// synchronize all groups with ldap directory
	function ldap_sync_all_groups() {
		global $prefs;
		global $logslib;

		if ($prefs['syncGroupsWithDirectory'] != 'y') {
			return false;
		}

		$users = $this->list_all_users();

		foreach ($users as $user) {
			$this->ldap_sync_groups($user, null);
		}
	}

	// Sync Tiki user with ldap directory
	function ldap_sync_user($user, $pass) {
		if ( $user == 'admin' ) return true;

		global $prefs;
		global $logslib;
		$ret=true;
		$this->init_ldap($user, $pass);

		if($prefs['auth_ldap_debug']=='y') $logslib->add_log('ldap','Syncing user with ldap');

		// sync user information
		if ($prefs['auth_method'] == 'ldap') {
			$this->disable_tiki_auth($user);
		}

		if ($prefs['syncUsersWithDirectory'] == 'y') {
			$this->ldap_sync_user_data($user, $this->ldap->get_user_attributes());
		}

		return $ret;
	}

	// Sync ldap data (name, email, country)
	function ldap_sync_user_data($user, $attributes) {
		global $prefs;

		$u = array('login' => $user);

		if (isset($attributes[$prefs['auth_ldap_nameattr']])) {
			$u['realName'] = $attributes[$prefs['auth_ldap_nameattr']];
		}

		if (isset($attributes[$prefs['auth_ldap_emailattr']])) {
			$u['email'] = $attributes[$prefs['auth_ldap_emailattr']];
		}

		if (isset($attributes[$prefs['auth_ldap_countryattr']])) {
			$u['country'] = $attributes[$prefs['auth_ldap_countryattr']];
		}

		if (count($u) > 1) {
			$this->set_user_fields($u);
		}
	}

	// Sync Tiki groups with ldap directory
	function ldap_sync_groups($user, $pass) {
		if ( $user == 'admin' ) return true;

		global $prefs;
		global $logslib;
		static $ldap_group_options = array();
		static $ext_dir = null;
		$ret = true;

		$this->init_ldap($user, $pass);
		$this->ldap->setOption('username', $user);
		$this->ldap->setOption('password', $pass);

		if($prefs['auth_ldap_debug']=='y') $logslib->add_log('ldap','Syncing group with ldap');
		$userattributes=$this->ldap->get_user_attributes(true);

		if ($prefs['syncGroupsWithDirectory'] == 'y' && $userattributes[$prefs['auth_ldap_group_corr_userattr']] != null) {
			// sync external group information of user
			$ldapgroups = array();

			if ($prefs['auth_ldap_group_external'] == 'y') {
				// External directory for groups
				if (!isset($ext_dir)) {
					$ldap_group_options=array('host' => $prefs['auth_ldap_group_host'],
							'port' => $prefs['auth_ldap_group_port'],
							'version' => $prefs['auth_ldap_group_version'],
							'starttls' => $prefs['auth_ldap_group_starttls'],
							'ssl' => $prefs['auth_ldap_group_ssl'],
							'basedn' => $prefs['auth_ldap_group_basedn'],
							'scope' => $prefs['auth_ldap_group_scope'],
							'userdn' => $prefs['auth_ldap_group_userdn'],
							'useroc' => $prefs['auth_ldap_group_useroc'],
							'userattr' => $prefs['auth_ldap_group_userattr'],
							'username' => $userattributes[$prefs['auth_ldap_group_corr_userattr']],
							'groupdn' => $prefs['auth_ldap_groupdn'],
							'groupattr' => $prefs['auth_ldap_groupattr'],
							'groupoc' => $prefs['auth_ldap_groupoc'],
							'groupnameattr' => $prefs['auth_ldap_groupnameatr'],
							'groupdescattr' => $prefs['auth_ldap_groupdescatr'],
							'groupmemberattr' => $prefs['auth_ldap_memberattr'],
							'groupmemberisdn' => $prefs['auth_ldap_memberisdn'],
							'usergroupattr' => $prefs['auth_ldap_usergroupattr'],
							'groupgroupattr' => $prefs['auth_ldap_groupgroupattr'],
							'debug' => $prefs['auth_ldap_group_debug']
					);

					if (empty($prefs['auth_ldap_group_adminuser'])) {
						// Anonymous
						$ldap_group_options['bind_type'] = 'default';
					} else {
						// Explicit
						$ldap_group_options['bind_type'] = 'explicit';
						$ldap_group_options['binddn'] = $prefs['auth_ldap_group_adminuser'];
						$ldap_group_options['bindpw'] =  $prefs['auth_ldap_group_adminpass'];
					}

					$ext_dir = new TikiLdapLib($ldap_group_options);
				}

				$ext_dir->setOption('username', $userattributes[$prefs['auth_ldap_group_corr_userattr']]);
				$ldapgroups = $ext_dir->get_groups(true);

			} else {
				if (!empty($prefs['auth_ldap_adminuser'])) {
					$this->ldap->setOption('bind_type', 'explicit');
					$this->ldap->setOption('binddn', $prefs['auth_ldap_adminuser']);
					$this->ldap->setOption('bindpw', $prefs['auth_ldap_adminpass']);
					$this->ldap->bind(true);
				}

				$ldapgroups = $this->ldap->get_groups(true);

				if (!empty($prefs['auth_ldap_adminuser'])) {
					$this->ldap->setOption('bind_type', $prefs['auth_ldap_type']);
					$this->ldap->bind(true);
				}
			}

			$this->ldap_sync_group_data($user, $ldapgroups);
		}

		return $ret;
	}

	// Sync Tiki groups with LDAP groups data
	function ldap_sync_group_data($user, $ldapgroups) {
		global $prefs;
		global $logslib;

		if (!count($ldapgroups)) {
			return;
		}

		$ldapgroups_simple=array();
		$tikigroups=$this->get_user_groups($user);
		foreach($ldapgroups as $group) {
			$gname=$group[$prefs['auth_ldap_groupattr']];
			$ldapgroups_simple[]=$gname; // needed later
			if($this->group_exists($gname) && !$this->group_is_external($gname)) { // group exists
				//check if we need to sync group information
				if(isset($group[$prefs['auth_ldap_groupdescattr']])) {
					$ginfo=$this->get_group_info($gname);
					if($group[$prefs['auth_ldap_groupdescattr']] != $ginfo['groupDesc']) {
						$this->set_group_description($gname,$group[$prefs['auth_ldap_groupdescattr']]);
					}
				}

			} else if(!$this->group_exists($gname)){ // create group
				if(isset($group[$prefs['auth_ldap_groupdescattr']])) {
					$gdesc=$group[$prefs['auth_ldap_groupdescattr']];
				} else {
					$gdesc='';
				}
				$logslib->add_log('ldap','Creating external group '.$gname);
				$this->add_group($gname,$gdesc,'',0,0,'','',0,'',0,0,'y');
			}

			// add user
			if(!in_array($gname,$tikigroups)) {
				$logslib->add_log('ldap','Adding user '.$user.' to external group '.$gname);
				$this->assign_user_to_group($user,$gname);
			}
		}

		// now clean up group membership if user has been unassigned from a group in ldap
		$extgroups=$this->get_user_external_groups($user);
		foreach($extgroups as $eg) {
			if(!in_array($eg,$ldapgroups_simple)) {
				$logslib->add_log('ldap','Removing user '.$user.' from external group '.$eg);
				$this->remove_user_from_group($user, $eg);
			}
		}
	}

	// called after create user or login from ldap
	function ldap_sync_user_and_groups($user,$pass) {
		$ret = true;
		$ret &= $this->ldap_sync_user($user, $pass);
		$ret &= $this->ldap_sync_groups($user, $pass);

		// Invalidate cache
		global $cachelib;
		require_once("lib/cache/cachelib.php");
		$cacheKey = 'user_details_'.$user;
		$cachelib->invalidate($cacheKey);

		return($ret);
	}

	function set_group_description($group,$description) {
		$query = "update `users_groups` set `groupDesc`=? where `groupName`=?";
		$result = $this->query($query, array($description,$group));
	}

	function group_is_external($group) {
		$gi=$this->get_group_info($group);
		if($gi['isExternal']=='y') {
			return true;
		}
		return false;
	}

	// simple function - no group inclusion or intertiki
	function get_user_external_groups($user) {
		$userid = $this->get_user_id($user);
		$query = "select u.`groupName` from `users_usergroups` u, `users_groups` g where u.`groupName`=g.`groupName` and u.`userId`=? and g.`isExternal`=?";
		$result=$this->query($query,array((int) $userid,'y'));
		$ret = array();
		while ($res = $result->fetchRow()) {
			 $ret[] = $res["groupName"];
		}
		return $ret;
	}

	// validate the user in the Tiki database
	function validate_user_tiki($user, $pass, $challenge, $response, $validate_phase=false) {
		global $prefs;

		// first verify that the user exists
		$query = "select * from `users_users` where binary `login` = ?";
		$result = $this->query($query, array($user) );

		if (!$result->numRows())
		{
			$query = "select * from `users_users` where upper(`login`) = ?";
			$result = $this->query($query, array(strtoupper( $user )));
			switch ($result->numRows()) {
				case 0: return array(USER_NOT_FOUND, $user);
				case 1: break;
				default: return array(USER_AMBIGOUS, $user);
			}
		}


		$res = $result->fetchRow();
		$user = $res['login'];

		// next verify the password with every hashes methods
		if ($prefs['feature_challenge'] == 'n' || empty($response)) {
			if (!empty($res['valid']) && $pass == $res['valid']) // used for validation of user account before activation
				return array(USER_VALID, $user);

			if ($res['waiting'] == 'u')
				return array(ACCOUNT_WAITING_USER, $user);
			if ($res['waiting'] == 'a')
				return array(ACCOUNT_DISABLED, $user);

			if ($res['hash'] == md5($user.$pass.trim($res['email']))) // very old method md5(user.pass.email), for compatibility
				return array(USER_VALID, $user);

			if ($res['hash'] == md5($user.$pass)) // old method md5(user.pass), for compatibility
				return array(USER_VALID, $user);

			if ($res['hash'] == md5($pass)) // normal method md5(pass)
				return array(USER_VALID, $user);

			if ($this->hash_pass($pass, $res['hash']) == $res['hash']) // new method (crypt-md5) and tikihash method (md5(pass))
				return array(USER_VALID, $user);

			return array(PASSWORD_INCORRECT, $user);
		} else {
			// Use challenge-reponse method
			// Compare pass against md5(user,challenge,hash)
			$hash = $this->getOne("select `hash` from `users_users` where binary `login`=?",
					array($user) );

			if (!isset($_SESSION["challenge"]))
				return array(false, $user);

			//print("pass: $pass user: $user hash: $hash <br />");
			//print("challenge: ".$_SESSION["challenge"]." challenge: $challenge<br />");
			//print("response : $response<br />");
			if ($response == md5($user . $hash . $_SESSION["challenge"])) {
				$this->update_lastlogin($user);
				return array(USER_VALID, $user);
			} else {
				return array(false, $user);
			}
		}

		return array(PASSWORD_INCORRECT, $user);
	}

	// ldap sync
	function sync_and_update_lastlogin($user, $pass) {
		global $prefs, $tikilib;
		$current = $this->getOne("select `currentLogin` from `users_users` where `login`= ?", array($user));
		$ret = $this->update_lastlogin($user, $current);

		if (is_null($current)) {
			// First time
			$current = 0;
		}

		// A LDAP synchronisation is not done in the 1st minute after login
		if ( $tikilib->now - $current >= 60 && ( $prefs['syncGroupsWithDirectory'] == 'y' || $prefs['syncUsersWithDirectory'] == 'y' ) ) {
			$ret &= $this->ldap_sync_user_and_groups($user, $pass);
		}

		return $ret;
	}

	// update the lastlogin status on this user
	function update_lastlogin($user, $current = null) {
		// Check
		if (is_null($current)) {
			$current = $this->getOne("select `currentLogin` from `users_users` where `login`= ?", array($user));
		}

		if (is_null($current)) {
			// First time
			$current = $this->now;
		}

		$query = "update `users_users` set `lastLogin`=?, `currentLogin`=?, `unsuccessful_logins`=? where `login`=?";
		$result = $this->query($query, array(
			(int)$current,
			(int)$this->now,
			0,
			$user
		) );

		return true;
	}

	// create a new user in the ldap directory
	function create_user_ldap($user, $pass) {
		// todo: kein pear::auth mehr! alles in pead::ldap2 abbilden
		global $tikilib, $prefs;

		$options = array();
		$options['url'] = $prefs['auth_ldap_url'];
		$options['host'] = $prefs['auth_ldap_host'];
		$options['port'] = $prefs['auth_ldap_port'];
		$options['scope'] = $prefs['auth_ldap_scope'];
		$options['basedn'] = $prefs['auth_ldap_basedn'];
		$options['userdn'] = $prefs['auth_ldap_userdn'];
		$options['userattr'] = $prefs['auth_ldap_userattr'];
		$options['useroc'] = $prefs['auth_ldap_useroc'];
		$options['groupdn'] = $prefs['auth_ldap_groupdn'];
		$options['groupattr'] = $prefs['auth_ldap_groupattr'];
		$options['groupoc'] = $prefs['auth_ldap_groupoc'];
		$options['memberattr'] = $prefs['auth_ldap_memberattr'];
		$options['memberisdn'] = ($prefs['auth_ldap_memberisdn'] == 'y');
		$options['binduser'] = $prefs['auth_ldap_adminuser'];
		$options['bindpw'] = $prefs['auth_ldap_adminpass'];

		// set additional attributes here
		$userattr = array();
		$userattr['email'] = ( $prefs['login_is_email'] == 'y' ) ? $user : $this->getOne("select `email` from `users_users` where `login`=?", array($user));

		// set the Auth options
		require_once("pear/Auth.php");
		$a = new Auth("LDAP", $options);

		// check if the login correct
		if ($a->addUser($user, $pass, $userattr) === true)
			$status = USER_VALID;

		// otherwise use the error status given back
		else
			$status = $a->getStatus();


		return $status;
	}

	
	function get_users_light($offset = 0, $maxRecords = -1, $sort_mode = 'login_asc', $find = '', $group = '') {
		// This is a lighter version of get_users_names designed for ajax checking of userrealnames
		global $prefs, $tiki_p_list_users, $tiki_p_admin;

		if ($tiki_p_list_users	!== 'y' && $tiki_p_admin != 'y') {
			return array();
		}

		$mid = '';
		$bindvars = array();
		if(!empty($group)) {
			if (!is_array($group)) {
				$group = array($group);
			}
			$mid = ', `users_usergroups` uug where uu.`userId`=uug.`userId` and uug.`groupName` in ('.implode(',',array_fill(0, count($group),'?')).')';
			$bindvars = $group;
		}
		if ( !empty($find) ) {
			$findesc = '%' . $find . '%';
			if (empty($mid)) {
				$mid .= " where uu.`login` like ?";
			} else {
				$mid .= " and uu.`login` like ?";
			}
			$bindvars[] = array($findesc);
		}
		
		$query = "select uu.`login` from `users_users` uu $mid order by ".$this->convertSortMode($sort_mode);
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		
		$ret = array();
		foreach ($result as $res) {
			$ret[$res['login']] = $this->clean_user($res["login"]);
		}
		if (!empty($finddesc) && $prefs['user_show_realnames'] == 'y') {
			$query = "select `user` from `tiki_user_preferences` where `prefName` = 'realName' and `value` like ?";
			$result = $this->fetchAll($query,array($findesc),$maxRecords,$offset);
			foreach ($result as $res) {
				if (!isset($ret[$res['user']])) {
					$ret[$res['user']] = $this->clean_user($res['user']);
				}
			}
		}
		asort($ret);
		return($ret);
	}
	
	function get_users_names($offset = 0, $maxRecords = -1, $sort_mode = 'login_asc', $find = '') {

		global $tiki_p_list_users, $tiki_p_admin;
		
		if ($tiki_p_list_users	!== 'y' && $tiki_p_admin != 'y') {
			return array();
		}

		// This function gets an array of user login names.
		if ( !empty($find) ) {
			$findesc = '%' . $find . '%';
			$mid = " where `login` like ?";
			$bindvars=array($findesc);
		} else {
			$mid = '';
			$bindvars=array();
		}

		$query = "select `login` from `users_users` $mid order by ".$this->convertSortMode($sort_mode);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res["login"];
		}

		return ($ret);
	}

	function get_users($offset = 0, $maxRecords = -1, $sort_mode = 'login_asc', $find = '', $initial = '', $inclusion=false, $group='', $email='') {

		global $tiki_p_list_users, $tiki_p_admin;
		
		if ($tiki_p_list_users	!== 'y' && $tiki_p_admin != 'y') { 
			return array();
		}

		$mid = '';
		$bindvars = array();
		$mmid = '';
		$mbindvars = array();
		// Return an array of users indicating name, email, last changed pages, versions, lastLogin

		//TODO : recurse included groups
		if(!empty($group)) {
			if (!is_array($group)) {
				$group = array($group);
			}
			$mid = ', `users_usergroups` uug where uu.`userId`=uug.`userId` and uug.`groupName` in ('.implode(',',array_fill(0, count($group),'?')).')';
			$mmid = $mid;
			$bindvars = $group;
			$mbindvars = $bindvars;
		}
		if( !empty($email) ) {
			$mid.= $mid == '' ? ' where' : ' and';
			$mid.= ' uu.`email` like ?';
			$mmid = $mid;
			$bindvars[] = '%'.$email.'%';
			$mbindvars[] = '%'.$email.'%';
		}

		if (! empty($find) ) {
			$mid.= $mid == '' ? ' where' : ' and';
			$mid.= " uu.`login` like ?";
			$mmid = $mid;
			$bindvars[] = '%'.$find.'%';
			$mbindvars[] = '%'.$find.'%';
		}

		if ( !empty($initial) ) {
			$mid = " where `login` like ?";
			$mmid = $mid;
			$bindvars = array($initial.'%');
			$mbindvars = $bindvars;
		}

		$query = "select uu.* from `users_users` uu $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `users_users` uu $mmid";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $mbindvars);

		foreach ( $ret as &$res ) {
			$aux = array();

			$res["user"] = $res["login"];
			$user = $res["user"];
			if ($inclusion) {
				$groups = $this->get_user_groups_inclusion($user);
			} else {
				$groups = $this->get_user_groups($user);
			}
			$res["groups"] = $groups;
			$res["age"] = $this->now - $res["registrationDate"];
			$res['user_information'] = $this->get_user_preference($user, 'user_information', 'public');

			$res['editable'] = $this->user_can_be_edited($user);
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/**
	 * @param string $edited_user : username (login) of the user that might be edited
	 * @param string $editing_user : username of user doing the editing (or logged in user if omitted)
	 * @return bool : true if $editing_user can edit $edited_user
	 */
	function user_can_be_edited($edited_user, $editing_user = '') {
		global $user;
		
		if (empty($editing_user)) {
			$editing_user = $user;
		}
		
		$editable = false;
		if ($this->user_has_permission($editing_user, 'tiki_p_admin')) {
			$editable = true;
		} else if ($this->user_has_permission($editing_user, 'tiki_p_admin_users') && !$this->user_has_permission($edited_user, 'tiki_p_admin')) {
			$editable = true;
		}
		return $editable;
	}

	function group_inclusion($group, $include) {
		$query = "insert into `tiki_group_inclusion`(`groupName`,`includeGroup`)
			values(?,?)";
		$result = $this->query($query, array($group, $include));
	}

	function get_included_groups($group, $recur=true) {
		$engroup = urlencode($group);
		if (!$recur || !isset($this->groupinclude_cache[$engroup])) {
			$query = "select `includeGroup` from `tiki_group_inclusion` where `groupName`=?";
			$result = $this->query($query, array($group));
			$ret = array();
			while ($res = $result->fetchRow()) {
				$ret[] = $res["includeGroup"];
				if ($recur) {
					$ret2 = $this->get_included_groups($res["includeGroup"]);
					$ret = array_merge($ret, $ret2);
				}
			}
			$back = array_unique($ret);
			if ($recur) {
				$this->groupinclude_cache[$engroup] = $back;
			}
			return $back;
		} else {
			return $this->groupinclude_cache[$engroup];
		}
	}
	function get_including_groups($group, $recur='n') {
		$query = 'select `groupName` from `tiki_group_inclusion` where `includeGroup`=? order by `groupName`';
		$result = $this->query($query, array($group));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['groupName'];
			if ($recur == 'y') {
				$ret = array_merge($ret, $this->get_including_groups($res['groupName']));
			}
		}
		if ($recur == 'y') {
			sort($ret);
		}
		return $ret;
	}
	function user_is_in_group($user, $group) {
		$user_details = $this->get_user_details($user);
		if (in_array($group, $user_details['groups'])) {
			return true;
		} else {
			return false;
		}
	}

	function remove_user_from_group($user, $group) {
		global $cachelib; require_once("lib/cache/cachelib.php");
		global $tikilib, $prefs;
		$cachelib->invalidate('user_details_'.$user);
		$tikilib->invalidate_usergroups_cache($user);

		$userid = $this->get_user_id($user);

		$query = "delete from `users_usergroups` where `userId` = ? and
			`groupName` = ?";
		$result = $this->query($query, array($userid, $group));
		$query = "update `users_users` set `default_group`=? where `login`=? and `default_group`=?";
		$this->query($query, array('Registered', $user, $group));
		
		if ($prefs['user_trackersync_groups'] == 'y') {
			$this->uncategorize_user_tracker_item($user, $group);	
		}
		
		$_SESSION['u_info']['group'] = 'Registered';
	}

	function remove_user_from_all_groups($user) {
		global $prefs;
		if ($prefs['user_trackersync_groups'] == 'y') {
			$groups = $this->get_user_groups($user);
			foreach ($groups as $group) {
				$this->uncategorize_user_tracker_item($user, $group);
			}	
		}
		$userid = $this->get_user_id($user);
		$query = "delete from `users_usergroups` where `userId` = ?";
		$result = $this->query($query, array($userid));
	}

	function get_groups($offset = 0, $maxRecords = -1, $sort_mode = 'groupName_asc', $find = '', $initial = '', $details="y", $inGroups='', $userChoice='') {

		$mid = "";
		$bindvars = array();
		if ($find) {
			$mid = " where `groupName` like ?";
			$bindvars[] = "%" . $find . "%";
		}

		if ($initial) {
			$mid = " where `groupName` like ?";
			$bindvars = array($initial . "%");
		}
		if ($inGroups) {
			$mid .= $mid? ' and ': ' where ';
			$mid .= '`groupName` in (';
			$cpt = 0;
			foreach ($inGroups as $grp=>$value) {
				if ($cpt++)
					$mid .= ',';
				$mid .= '?';
				$bindvars[] = $grp;
				}
			$mid .= ')';
		}
		if ($userChoice) {
			$mid .= $mid? ' and ': ' where ';
			$mid .= '`userChoice` = ?';
			$bindvars[] = 'y';
		}

		$query = "select * from `users_groups` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `users_groups` $mid";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);

		foreach ( $ret as &$res ) {
			if ($details == "y") {
				$perms = $this->get_group_permissions($res['groupName']);
				$res['perms'] = $perms;
				$res['permcant'] = count($perms);
				$groups = $this->get_included_groups($res['groupName']);
				$res['included'] = $groups;
			}
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_all_users() {
		global $cachelib, $tiki_p_list_users, $tiki_p_admin;

		if ($tiki_p_list_users	!== 'y' && $tiki_p_admin != 'y') {
			return array();
		}

		if (! $users = $cachelib->getSerialized("userslist")) {
			$users = array();
			$result = $this->query("select `login`,`userId` from `users_users` order by `login`", array());
			while ($res = $result->fetchRow()) {
				$users["{$res['userId']}"] = $res['login'];
			}
			$cachelib->cacheItem("userslist",serialize($users));
		}

		return $users;
	}

	function list_all_groups() {
		global $cachelib;
		if (! $groups = $cachelib->getSerialized("grouplist")) {
			$groups = array();
			$result = $this->query("select `groupName` from `users_groups` order by `groupName`", array());
			while ($res = $result->fetchRow()) {
				$groups[] = $res['groupName'];
			}
			$cachelib->cacheItem("grouplist",serialize($groups));
		}

		return $groups;
	}

	function list_all_groupIds() {
		global $cachelib;
		if (! $groups = $cachelib->getSerialized("groupIdlist")) {
			$groups = $this->fetchAll("select `id`, `groupName` from `users_groups` order by `groupName`", array());
			$cachelib->cacheItem("groupIdlist",serialize($groups));
		}

		return $groups;
	}

	function list_can_include_groups($group) {

		$list = array();
		$query = "select `groupName` from `users_groups`";
		$result = $this->query($query);
		while($res = $result->fetchRow()) {
			if($res['groupName'] != $group) {
				$includedGroups = $this->get_included_groups($res['groupName']);
				if(!in_array($group, $includedGroups)) {
					$list[] = $res['groupName'];
				}
			}
		}
		return $list;
	}


	function remove_user($user) {
		global $cachelib;
		if ( $user == 'admin' ) return false;

		$userId = $this->getOne("select `userId` from `users_users` where `login` = ?", array($user));

		$groupTracker = $this->get_tracker_usergroup( $user );
		if( $groupTracker && $groupTracker['usersTrackerId'] ) {
			$trklib = TikiLib::lib('trk');

			$itemId = $trklib->get_item_id( $groupTracker['usersTrackerId'], $groupTracker['usersFieldId'], $user );
			if( $itemId ) {
				$trklib->remove_tracker_item( $itemId );
			}
		}

		$tracker = $this->get_usertracker( $userId );
		if( $tracker && $tracker['usersTrackerId'] ) {
			$trklib = TikiLib::lib('trk');

			$itemId = $trklib->get_item_id( $tracker['usersTrackerId'], $tracker['usersFieldId'], $user );
			if( $itemId ) {
				$trklib->remove_tracker_item( $itemId );
			}
		}

		$query = "delete from `users_users` where binary `login` = ?";
		$result = $this->query($query, array( $user ) );
		$query = "delete from `users_usergroups` where `userId`=?";
		$result = $this->query($query, array( $userId ) );
		$query = "delete from `tiki_user_login_cookies` where `userId`=?";
		$result = $this->query($query, array( $userId ) );
		$query = "delete from `tiki_user_watches` where binary `user`=?";
		$result = $this->query($query, array($user));
		$query = "delete from `tiki_user_preferences` where binary `user`=?";
		$result = $this->query($query, array($user));
		$query = "delete from `tiki_newsletter_subscriptions` where binary `email`=? and `isUser`=?";
		$result = $this->query($query, array($user, 'y'));

		$cachelib->invalidate('userslist');
		return true;
	}

	function change_login($from,$to) {
		global $cachelib;
		if ( $from == 'admin' ) return false;

		$userId = $this->getOne("select `userId` from `users_users` where `login` = ?", array($from));
		if ($userId) {
			$this->query("update `users_users` set `login`=? where `userId` = ?", array($to,(int)$userId));
			$this->query("update `tiki_wiki_attachments` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_webmail_messages` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_webmail_contacts` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_webmail_contacts_fields` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_userpoints` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_userfiles` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_watches` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_votings` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_tasks` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_tasks` set `creator`=? where `creator`=?", array($to,$from));
			$this->query("update `tiki_user_tasks_history` set `lasteditor`=? where `lasteditor`=?", array($to,$from));
			$this->query("update `tiki_user_taken_quizzes` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_quizzes` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_preferences` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_postings` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_notes` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_menus` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_mail_accounts` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_bookmarks_urls` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_bookmarks_folders` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_assigned_modules` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_tags` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_suggested_faq_questions` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_submissions` set `author`=? where `author`=?", array($to,$from));
			$this->query("update `tiki_shoutbox` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_sessions` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_semaphores` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_received_pages` set `receivedFromUser`=? where `receivedFromUser`=?", array($to,$from));
			$this->query("update `tiki_received_articles` set `author`=? where `author`=?", array($to,$from));
			$this->query("update `tiki_private_messages` set `poster`=? where `poster`=?", array($to,$from));
			$this->query("update `tiki_private_messages` set `toNickname`=? where `toNickname`=?", array($to,$from));
			$this->query("update `tiki_pages` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_pages` set `creator`=? where `creator`=?", array($to,$from));
			$this->query("update `tiki_page_footnotes` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_newsletters` set `author`=? where `author`=?", array($to,$from));
			$this->query("update `tiki_minical_events` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_minical_topics` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_mailin_accounts` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_live_support_requests` set `operator`=? where `operator`=?", array($to,$from));
			$this->query("update `tiki_live_support_requests` set `tiki_user`=? where `tiki_user`=?", array($to,$from));
			$this->query("update `tiki_live_support_requests` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_live_support_operators` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_live_support_messages` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_live_support_messages` set `username`=? where `username`=?", array($to,$from));
			$this->query("update `tiki_images` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_history` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_galleries` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_forums_reported` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_forums_queue` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_forums` set `moderator`=? where `moderator`=?", array($to,$from));
			$this->query("update `tiki_forum_reads` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_files` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_files` set `lastModifUser`=? where `lastModifUser`=?", array($to,$from));
			$this->query("update `tiki_files` set `lockedby`=? where `lockedby`=?", array($to,$from));
			$this->query("update `tiki_file_galleries` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_file_drafts` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_copyrights` set `userName`=? where `userName`=?", array($to,$from));
			$this->query("update `tiki_comments` set `userName`=? where `userName`=?", array($to,$from));
			$this->query("update `tiki_chat_users` set `nickname`=? where `nickname`=?", array($to,$from));
			$this->query("update `tiki_chat_messages` set `poster`=? where `poster`=?", array($to,$from));
			$this->query("update `tiki_chat_channels` set `moderator`=? where `moderator`=?", array($to,$from));
			$this->query("update `tiki_calendars` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_calendar_roles` set `username`=? where `username`=?", array($to,$from));
			$this->query("update `tiki_calendar_items` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_blogs` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_blog_posts` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_banning` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_banners` set `client`=? where `client`=?", array($to,$from));
			$this->query("update `tiki_articles` set `author`=? where `author`=?", array($to,$from));
			$this->query("update `tiki_actionlog` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `messu_messages` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `messu_messages` set `user_from`=? where `user_from`=?", array($to,$from));
			$this->query("update `tiki_newsletter_subscriptions` set `email`=? where `email`=? and `isUser`=?", array($to,$from, 'y'));
			$this->query("update `tiki_friends` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_friends` set `friend`=? where `friend`=?", array($to,$from));
			$this->query("update `tiki_friendship_requests` set `userFrom`=? where `userFrom`=?", array($to,$from));
			$this->query("update `tiki_friendship_requests` set `userTo`=? where `userTo`=?", array($to,$from));
			$this->query("update `tiki_freetagged_objects` set `user`=? where `user`=?", array($to,$from));

			$this->query('update `tiki_tracker_item_fields`ttif left join `tiki_tracker_fields` ttf on (ttif.`fieldId`=ttf.`fieldId`) set `value`=? where ttif.`value`=? and ttf.`type`=?', array($to, $from, 'u'));
			$this->query('update `tiki_tracker_items` set `createdBy`=? where `createdBy`=?', array($to, $from));
			$this->query('update `tiki_tracker_items` set `lastModifBy`=? where `lastModifBy`=?', array($to, $from));

			$result = $this->query("select `fieldId`, `itemChoices` from `tiki_tracker_fields` where `type`='u'");

			while($res = $result->fetchRow())
			{
				$this->query("update `tiki_tracker_item_fields` set `value`=? where `value`=? and `fieldId`=?", array($to,$from,$res['fieldId']));

				$u = ($res['itemChoices'] != '' ) ? unserialize($res['itemChoices']) : array();

				if($value=array_search($from, $u))
				{
					$u[$value] = $to ;
					$u = serialize($u);
					$this->query("update `tiki_tracker_fields` set `itemChoices`=? where `fieldId`=?", array($u,$res['fieldId']));
				}
			}
			$cachelib->invalidate('userslist');
			return true;
		} else {
			return false;
		}
	}

	function remove_group($group) {
		if ( $group == 'Anonymous' || $group == 'Registered' ) return false;

		$query = "delete from `tiki_group_inclusion` where `groupName` = ? or `includeGroup` = ?";
		$result = $this->query($query, array($group, $group));

		$query = array();
		$query[] = "delete from `users_groups` where `groupName` = ?";
		$query[] = "delete from `users_usergroups` where `groupName` = ?";
		$query[] = "delete from `users_grouppermissions` where `groupName` = ?";
		$query[] = "delete from `users_objectpermissions` where `groupName` = ?";
		$query[] = "delete from `tiki_newsletter_groups` where `groupName` = ?";
		$query[] = "delete from `tiki_group_watches` where `group` = ?";
		foreach ( $query as $q )
			$this->query($q, array($group));
		$this->query("update `users_users` set `default_group`=? where `default_group`=?", array('Registered', $group));

		global $cachelib;
		$cachelib->invalidate('grouplist');
		$cachelib->invalidate('group_theme_'.$group);

		return true;
	}

	function get_user_default_group($user) {
		if (!isset($user)) {
			return 'Anonymous';
		}
		if ($user == $_SESSION['u_info']['login']) {
			if (isset($_SESSION['u_info']['group']) && is_string($_SESSION['u_info']['group'])) {
				return $_SESSION['u_info']['group'];
			} elseif (isset($_SESSION['u_info']['group']['groupName']) && is_string($_SESSION['u_info']['group']['groupName'])) {
				return $_SESSION['u_info']['group']['groupName'];
			}
		}
		$query = "select `default_group` from `users_users` where `login` = ?";
		$result = $this->getOne($query, array($user));
		$ret = '';
		if (!is_null($result) && $result != "") {
			$ret = $result;
		} else {
			$groups = $this->get_user_groups($user);
			foreach ($groups as $gr) {
				if ($gr != "Anonymous" and $gr != "Registered" and $gr != "") {
					$ret = $gr;
					break;
				}
			}
			if (!$ret) {
				$ret = "Registered";
			}
		}
		return $ret;
	}
	function get_user_default_homepage($user) {
		$result = $this->get_user_default_group($user);
		if (!is_null($result)) {
			$home = $this->get_group_home($result);
			if ($home != '')
				return $this->best_multilingual_page($home);
		}
		$query = "select g.`groupHome`, g.`groupName` from `users_usergroups` as gu, `users_users` as u, `users_groups`as g where gu.`userId`= u.`userId` and u.`login`=? and gu.`groupName`= g.`groupName` and g.`groupHome` != '' and g.`groupHome` is not null";
		$result = $this->query($query,array($user));
		$home = '';
		while ($res = $result->fetchRow()) {
			if ($home != '') {
				$groups = $this->get_included_groups($res["groupName"]);
				if (in_array($group, $groups)) {
					$home = $res["groupHome"];
					$group = $res["groupName"];
				}
			}
			$home = $res["groupHome"];
			$group = $res["groupName"];
		}
		return $this->best_multilingual_page($home);
	}
	function best_multilingual_page($page) {
		global $prefs;
		if ($prefs['feature_multilingual'] != 'y') {
			return ($page);
		}
		$info = $this->get_page_info($page);
		global $multilinguallib; include_once ("lib/multilingual/multilinguallib.php");
		$bestLangPageId = $multilinguallib->selectLangObj('wiki page', $info['page_id'], $prefs['language']);
		if ($info['page_id'] == $bestLangPageId) {
			return $page;
		}
		return $this->get_page_name_from_id($bestLangPageId);
	}
	function get_user_default_homepage2($user) {
		global $prefs;
		if ($prefs['useGroupHome'] == 'y') {
			$groupHome = $this->get_user_default_homepage($user);
			if ($groupHome)
				$p = $groupHome;
 			else
				$p = $prefs['wikiHomePage'];
		} else {
			$p = $prefs['wikiHomePage'];
		}
		return $p;
	}

	/* Returns a theme/style for this ithe default group of the current user. */
	function get_user_group_theme() {
		global $user;
		$group = $this->get_user_default_group($user);

		global $cachelib; require_once("lib/cache/cachelib.php");
		$k = 'group_theme_'.$group;

		if ( $data = $cachelib->getCached($k) ) {
			$return = $data;
		} elseif ( ! empty($group) ) {
			$query = 'select `groupTheme` from `users_groups` where `groupName` = ?';
			$return = $this->getOne($query, array($group));
			$cachelib->cacheItem($k, $return);
		}
		return $return;
	}

	/* Returns a default category for user's default_group
	*/
	function get_user_group_default_category($user) {
		$query = "select `groupDefCat` from `users_groups` ug, `users_users` uu where `login` = ? and ug.`groupName` = uu.`default_group`";
		$result = $this->getOne($query, array($user));
		return $result;
	}

	//modified get_user_groups() to know if the user is part of the group directly or through groups inclusion
	function get_user_groups_inclusion($user) {
		$userid = $this->get_user_id($user);

		$query = "select `groupName` from `users_usergroups` where `userId`=?";
		$result = $this->query($query, array((int)$userid));
		$real = array(); //really assigned groups (not (only) included)
		$ret = array();
		while ($res = $result->fetchRow()) {
			$real[] = $res["groupName"];
			foreach ($this->get_included_groups($res["groupName"]) as $group) {
				$ret[$group] = "included";
			}
		}
		foreach ($real as $group) {
			$ret[$group] = "real";
		}
		return $ret;
	}

	function get_group_home($group) {
		$query = "select `groupHome` from `users_groups` where `groupName`=?";
		$result = $this->getOne($query,array($group));
		$ret ='';
		if (!is_null($result)) {
			$ret = $result;
		}
		return $ret;
	}

	/**
	 * Return information about users that belong to a 
	 * specific group
	 * 
	 * @param string $group group name
	 * @param int $offset
	 * @param int $max
	 * @param string $what which user fields to retrieve
	 * @param string $sort_mode
	 * @return array list of users
	 */
	function get_group_users($group, $offset = 0, $max=-1, $what='login', $sort_mode='login_asc') {
		global $prefs;
		$w = $what=='*'? 'uu.*, ug.`created`, ug.`expire` ': "uu.`$what`"; 
		$query = "select $w from `users_users` uu, `users_usergroups` ug where uu.`userId`=ug.`userId` and `groupName`=? order by ".$this->convertSortMode($sort_mode);
		$result = $this->fetchAll($query,$group, $max, $offset);
		$ret = array();
		foreach ($result as $res) {
			$ret[] = ($what == '*') ? $res: $res[$what];
		}
		return $ret;
	}

	function get_user_info($user, $inclusion = false, $field = 'login') {
		global $prefs;
		if ( $field == 'userId' ) $user = (int)$user;
		elseif ( $field != 'login' ) return false;

		$result = $this->query("select * from `users_users` where `$field`=?", array($user));
		if( $res = $result->fetchRow() ) {
			$res['groups'] = ( $inclusion ) ? $this->get_user_groups_inclusion($res['login']) : $this->get_user_groups($res['login']);
			$res['age'] = ( ! isset($res['registrationDate']) ) ? 0 : $this->now - $res['registrationDate'];
			if ( $prefs['login_is_email'] == 'y' && isset($res['login']) && $res['login'] != 'admin' ) $res['email'] = $res['login'];

			$res['editable'] = $this->user_can_be_edited($res['login']);

			return $res;
		}
	}

	function get_userid_info($user, $inclusion = false) {
		return $this->get_user_info($user, $inclusion, 'userId');
	}

	// UNRELIABLE. In particular, lastLogin and currentLogin aren't properly maintained due to missing user_details_ cache invalidation
	// refactoring to use new cachelib instead of global var in memory - batawata 2006-02-07
	function get_user_details($login) {
		global $cachelib;
		require_once("lib/cache/cachelib.php");

		$cacheKey = 'user_details_'.$login;

		if ( ! $user_details = $cachelib->getSerialized($cacheKey)) {
			$user_details = array();

			$query = 'SELECT `userId` , `login`, `email` , `lastLogin` , `currentLogin` , `registrationDate` , `created` , `avatarName` , `avatarSize` , `avatarFileType` , `avatarLibName` , `avatarType` FROM `users_users` WHERE `login` = ?';

			$result = $this->query($query, array($login));

			$user_details['info'] = $result->fetchRow();

			$query = 'SELECT `prefName` , `value` FROM `tiki_user_preferences` WHERE `user` = ?';
			$result = $this->query($query, array($login));

			$user_details['preferences'] = array();
			$aUserPrefs = array('realName','homePage','country');
			while ( $row = $result->fetchRow() ) {
				$user_details['preferences'][$row['prefName']] = $row['value'];

				// atention: this is redundant, for intertiki slave mode
				// we insert, delete and insert again this information,
				// because of nature of user information as being preferences
				if (in_array($row['prefName'], $aUserPrefs)) {
					$user_details['info'][$row['prefName']] = $row['value'];
				}

			}

			$user_details['groups'] = $this->get_user_groups($login);

			$cachelib->cacheItem($cacheKey, serialize($user_details));

			global $user_preferences;
			$user_preferences[$login] = $user_details['preferences'];
		}

		return $user_details;
	}

	function set_default_group($user,$group) {
		// if user is not in group, assign user to group before setting default group
		$user_groups = $this->get_user_groups($user);
		if (!in_array($group, $user_groups) && !empty($group)) {
			$this->assign_user_to_group($user, $group);
		}
		$query = "update `users_users` set `default_group` = ?
			where `login` = ?";
		$this->query($query, array($group, $user));
	}
	function set_email_group($user, $email) {
		$query = 'select `id`, `groupName`, `emailPattern` from `users_groups` where `emailPattern`!=?';
		$groups = $this->fetchAll($query, array(''));
		$nb = 0;
		if (empty($groups)) {
			return 0;
		}
		$userGroups = $this->get_user_groups_inclusion($user);
		foreach ($groups as $group) {
			if (!isset($userGroups[$group['groupName']]) && preg_match($group['emailPattern'], $email)) {
				$this->assign_user_to_group($user, $group['groupName']);
				$this->set_default_group($user, $group['groupName']);
				++$nb;
			}
		}
		return $nb;
	}
	function refresh_set_email_group() {
		$users = $this->list_users();
		$nb = 0;
		foreach($users['data'] as $user) {
			$nb += $this->set_email_group($user['login'], $user['email']);
		}
		return $nb;
	}

	function change_permission_level($perm, $level) {
		$query = "update `users_permissions` set `level` = ? where `permName` = ?";
		$this->query($query, array($level, $perm));

		global $cachelib;
		$cachelib->empty_type_cache("fgals_perms");

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->empty_menu_cache();
	}

	function assign_level_permissions($group, $level) {
		$query = "select `permName` from `users_permissions` where `level` = ?";
		$result = $this->query($query, array($level));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$this->assign_permission_to_group($res['permName'], $group);
		}

		global $cachelib;
		$cachelib->empty_type_cache("fgals_perms");
		$cachelib->invalidate("groupperms_$group");

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->empty_menu_cache();
	}

	function remove_level_permissions($group, $level) {
		$query = "select `permName` from `users_permissions` where `level` = ?";
		$result = $this->query($query, array($level));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$this->remove_permission_from_group($res['permName'], $group);
		}

		global $cachelib;
		$cachelib->empty_type_cache("fgals_perms");
		$cachelib->invalidate("groupperms_$group");

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->empty_menu_cache();
	}

	function create_dummy_level($level) {
		$query = "delete from `users_permissions` where `permName` = ?";
		$result = $this->query($query, array(''));
		$query = "insert into `users_permissions`(`permName`, `level`) values('', ?)";
		$this->query($query, array($level));

		global $cachelib;
		$cachelib->empty_type_cache("fgals_perms");

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->empty_menu_cache();
	}

	function get_permission_levels() {
		$query = "select distinct(`level`) from `users_permissions`";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res['level'];
		}

		return $ret;
	}

	function get_tracker_usergroup($user) {
		$lastRes = '';
		$group = $this->get_user_default_group($user);
		if (!empty($group)) {
			$lastRes = $this->get_usertrackerid($group);
		}
		if (!$lastRes) {
			$groups = $this->get_user_groups($user);
			$query = 'select `groupName`, `usersTrackerId`, `usersFieldId` from `users_groups` where `groupName` in ( '.implode(' , ',array_fill(0,count($groups),'?')).' ) and `groupName` != ? and `usersTrackerId` > 0';
			$groups[] = 'Anonymous';
			$result = $this->query($query, $groups);
			while ($res = $result->fetchRow()) {
				$lastRes = $res;
				if ($res['groupName'] != 'Registered')
					return 	$res ;
			}
		}
		return $lastRes;
	}

	function get_grouptrackerid($group) {
		$res = $this->query("select `groupTrackerId`,`groupFieldId` from `users_groups` where `groupName`=?",array($group));
		$ret = $res->fetchRow();
		if (!$ret['groupTrackerId'] or !$ret['groupFieldId']) {
			$groups = $this->get_included_groups($group);
			foreach ($groups as $gr) {
				$res = $this->query("select `groupTrackerId`,`groupFieldId` from `users_groups` where `groupName`=?",array($gr));
				$ret = $res->fetchRow();
				if ($ret['groupTrackerId'] and $ret['groupFieldId']) {
					return $ret;
				}
			}
		} else {
			return $ret;
		}
		return false;
	}

	function get_usertrackerid($group) {
		$res = $this->query("select `usersTrackerId`,`usersFieldId` from `users_groups` where `groupName`=?",array($group));
		$ret = $res->fetchRow();
		if (!$ret['usersTrackerId'] or !$ret['usersFieldId']) {
			$groups = $this->get_included_groups($group);
			foreach ($groups as $gr) {
				$res = $this->query("select `usersTrackerId`,`usersFieldId` from `users_groups` where `groupName`=?",array($gr));
				$ret = $res->fetchRow();
				if ($ret['usersTrackerId'] and $ret['usersFieldId']) {
					return $ret;
				}
			}
		} else {
			return $ret;
		}
		return false;
	}


	function get_usertracker($uid) {
		if( $utr = $this->get_userid_info($uid) ) {
			$utr["usersTrackerId"] = '';
			foreach ($utr['groups'] as $gr) {
				$utrid = $this->get_usertrackerid($gr);
				if ($utrid['usersTrackerId'] and $utrid['usersFieldId']) {
					$utrid['group'] = $gr;
					$utrid['user'] = $utr['login'];
					$utr = $utrid;
					break;
				}
			}
			return $utr;
		}
	}

	function get_enabled_permissions()
	{
		global $prefs;

		$raw = $this->get_raw_permissions();
		$out = array();

		foreach ($raw as $permission) {
			$valid = empty($permission['prefs']);

			if (! $valid) {
				foreach ($permission['prefs'] as $name) {
					if ($prefs[$name] == 'y') {
						$valid = true;
						break;
					}
				}
			}

			if ($valid) {
				$out[$permission['name']] = $permission;
			}
		}

		return $out;
	}

	function get_permission_names_for($type)
	{
		$raw = $this->get_raw_permissions();
		$out = array();

		foreach ($raw as $permission) {
			if ($permission['type'] == $type) {
				$out[] = $permission['name'];
			}
		}

		return $out;
	}

	private function get_raw_permissions()
	{
		global $prefs;
		$cachelib = TikiLib::lib('cache');

		if ($permissions = $cachelib->getSerialized('rawpermissions' . $prefs['language'])) {
			return $permissions;
		}

		$permissions = array(
			array(
				'name' => 'tiki_p_admin_calendar', 
				'description' => array('Can create/admin calendars'),
				'level' => 'admin',
				'type' => 'calendar',
				'admin' => true,
				'prefs' => array('feature_calendar'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_add_events',
				'description' => tra('Can add events in the calendar'),
				'level' => 'registered',
				'type' => 'calendar',
				'admin' => false,
				'prefs' => array('feature_calendar'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_change_events',
				'description' => tra('Can change events in the calendar'),
				'level' => 'registered',
				'type' => 'calendar',
				'admin' => false,
				'prefs' => array('feature_calendar'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_calendar',
				'description' => tra('Can browse the calendar'),
				'level' => 'basic',
				'type' => 'calendar',
				'admin' => false,
				'prefs' => array('feature_calendar'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_events',
				'description' => tra('Can view events details'),
				'level' => 'registered',
				'type' => 'calendar',
				'admin' => false,
				'prefs' => array('feature_calendar'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_calendar_add_my_particip',
				'description' => tra('Can add own user to the participants'),
				'level' => 'registered',
				'type' => 'calendar',
				'admin' => false,
				'prefs' => array('feature_calendar'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_calendar_add_guest_particip',
				'description' => tra('Can add guest to the participants'),
				'level' => 'registered',
				'type' => 'calendar',
				'admin' => false,
				'prefs' => array('feature_calendar'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_tiki_calendar',
				'description' => tra('Can view Tiki tools calendar'),
				'level' => 'basic',
				'type' => 'calendar',
				'admin' => false,
				'prefs' => array('feature_action_calendar'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_chat',
				'description' => tra('Administrator can create channels, remove channels, etc'),
				'level' => 'editors',
				'type' => 'chat',
				'admin' => true,
				'prefs' => array('feature_minichat', 'feature_live_support'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_chat',
				'description' => tra('Can use the chat system'),
				'level' => 'registered',
				'type' => 'chat',
				'admin' => false,
				'prefs' => array('feature_minichat', 'feature_live_support'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_cms',
				'description' => tra('Can admin the articles'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => true,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_approve_submission',
				'description' => tra('Can approve submissions'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_articles_admin_topics',
				'description' => tra('Can admin article topics'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_articles_admin_types',
				'description' => tra('Can admin article types'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_articles_read_heading',
				'description' => tra('Can read article headings'),
				'level' => 'basic',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_autoapprove_submission',
				'description' => tra('Submitted articles automatically approved'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_article',
				'description' => tra('Can edit articles'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_edit_submission',
				'description' => tra('Can edit submissions'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_read_article',
				'description' => tra('Can read articles'),
				'level' => 'basic',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_remove_article',
				'description' => tra('Can remove articles'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_remove_submission',
				'description' => tra('Can remove submissions'),
				'level' => 'editors',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_submit_article',
				'description' => tra('Can submit articles'),
				'level' => 'basic',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_rate_article',
				'description' => tra('Can rate articles'),
				'level' => 'basic',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_topic_read',
				'description' => tra('Can read a topic (Applies only to individual topic perms)'),
				'level' => 'basic',
				'type' => 'cms',
				'admin' => false,
				'prefs' => array('feature_articles'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_contribution',
				'description' => tra('Can admin contributions'),
				'level' => 'admin',
				'type' => 'contribution',
				'admin' => true,
				'prefs' => array('feature_contribution'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_directory',
				'description' => tra('Can admin the directory'),
				'level' => 'editors',
				'type' => 'directory',
				'admin' => true,
				'prefs' => array('feature_directory'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_directory_cats',
				'description' => tra('Can admin directory categories'),
				'level' => 'editors',
				'type' => 'directory',
				'admin' => false,
				'prefs' => array('feature_directory'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_directory_sites',
				'description' => tra('Can admin directory sites'),
				'level' => 'editors',
				'type' => 'directory',
				'admin' => false,
				'prefs' => array('feature_directory'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_autosubmit_link',
				'description' => tra('Submitted links are valid'),
				'level' => 'editors',
				'type' => 'directory',
				'admin' => false,
				'prefs' => array('feature_directory'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_submit_link',
				'description' => tra('Can submit sites to the directory'),
				'level' => 'basic',
				'type' => 'directory',
				'admin' => false,
				'prefs' => array('feature_directory'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_validate_links',
				'description' => tra('Can validate submitted links'),
				'level' => 'editors',
				'type' => 'directory',
				'admin' => false,
				'prefs' => array('feature_directory'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_directory',
				'description' => tra('Can use the directory'),
				'level' => 'basic',
				'type' => 'directory',
				'admin' => false,
				'prefs' => array('feature_directory'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_faqs',
				'description' => tra('Can admin faqs'),
				'level' => 'editors',
				'type' => 'faqs',
				'admin' => true,
				'prefs' => array('feature_faqs'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_suggest_faq',
				'description' => tra('Can suggest faq questions'),
				'level' => 'basic',
				'type' => 'faqs',
				'admin' => false,
				'prefs' => array('feature_faqs'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_faqs',
				'description' => tra('Can view faqs'),
				'level' => 'basic',
				'type' => 'faqs',
				'admin' => false,
				'prefs' => array('feature_faqs'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin',
				'description' => tra('Administrator can manage users, groups and permissions and all features'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => true,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_users',
				'description' => tra('Can admin users'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_access_closed_site',
				'description' => tra('Can access site when closed'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_banners',
				'description' => tra('Administrator can admin banners'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_banners'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_banning',
				'description' => tra('Can ban users or ips'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_banning'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_dynamic',
				'description' => tra('Can admin the dynamic content system'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_dynamic_content'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_integrator',
				'description' => tra('Can admin integrator repositories and rules'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_integrator'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_mailin',
				'description' => tra('Can admin mail-in accounts'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_mailin'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_objects',
				'description' => tra('Can edit object permissions'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_rssmodules',
				'description' => tra('Can admin external feeds'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_clean_cache',
				'description' => tra('Can clean cache'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_create_css',
				'description' => tra('Can create new css suffixed with -user'),
				'level' => 'registered',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_editcss'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_detach_translation',
				'description' => tra('Can remove association between two pages in a translation set'),
				'level' => 'registered',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_multilingual'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_cookies',
				'description' => tra('Can admin cookies'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_languages',
				'description' => tra('Can edit translations and create new languages'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_menu',
				'description' => tra('Can edit menu'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_menu_option',
				'description' => tra('Can edit menu option'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_templates',
				'description' => tra('Can edit site templates'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_edit_templates'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_search',
				'description' => tra('Can search'),
				'level' => 'basic',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_site_report',
				'description' => tra('Can report a link to the webmaster'),
				'level' => 'basic',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_site_report'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_subscribe_groups',
				'description' => tra('Can subscribe to groups'),
				'level' => 'registered',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_tell_a_friend',
				'description' => tra('Can send a link to a friend'),
				'level' => 'Basic',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_tell_a_friend'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_share',
				'description' => tra('Can share a page (email, twitter, facebook, message, forums)'),
				'level' => 'basic',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_share'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_use_HTML',
				'description' => tra('Can use HTML in pages'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_wiki_allowhtml'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_actionlog',
				'description' => tra('Can view action log'),
				'level' => 'registered',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_actionlog'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_actionlog_owngroups',
				'description' => tra('Can view action log for users of his own groups'),
				'level' => 'registered',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_actionlog'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_integrator',
				'description' => tra('Can view integrated repositories'),
				'level' => 'basic',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_integrator'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_referer_stats',
				'description' => tra('Can view referrer stats'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_referer_stats'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_stats',
				'description' => tra('Can view site stats'),
				'level' => 'basic',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_stats'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_templates',
				'description' => tra('Can view site templates'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_edit_templates'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_blog_admin',
				'description' => tra('Can admin blogs'),
				'level' => 'editors',
				'type' => 'blogs',
				'admin' => true,
				'prefs' => array('feature_blogs'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_assign_perm_blog',
				'description' => tra('Can assign perms to blog'),
				'level' => 'admin',
				'type' => 'blogs',
				'admin' => false,
				'prefs' => array('feature_blogs'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_blog_post',
				'description' => tra('Can post to a blog'),
				'level' => 'registered',
				'type' => 'blogs',
				'admin' => false,
				'prefs' => array('feature_blogs'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_create_blogs',
				'description' => tra('Can create a blog'),
				'level' => 'editors',
				'type' => 'blogs',
				'admin' => false,
				'prefs' => array('feature_blogs'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_read_blog',
				'description' => tra('Can read blogs'),
				'level' => 'basic',
				'type' => 'blogs',
				'admin' => false,
				'prefs' => array('feature_blogs'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_blog_post_view_ref',
				'description' => tra('Can view in module and feed the blog posts'),
				'level' => 'basic',
				'type' => 'blogs',
				'admin' => false,
				'prefs' => array('feature_blogs'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_blog_view_ref',
				'description' => tra('Can view in module and feed the blog'),
				'level' => 'basic',
				'type' => 'blogs',
				'admin' => false,
				'prefs' => array('feature_blogs'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_file_galleries',
				'description' => tra('Can admin file galleries'),
				'level' => 'editors',
				'type' => 'file galleries',
				'admin' => true,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_assign_perm_file_gallery',
				'description' => tra('Can assign perms to file gallery'),
				'level' => 'admin',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_batch_upload_file_dir',
				'description' => tra('Can use Directory Batch Load'),
				'level' => 'editors',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_batch_upload_files',
				'description' => tra('Can upload zip files with files'),
				'level' => 'editors',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_create_file_galleries',
				'description' => tra('Can create file galleries'),
				'level' => 'editors',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_download_files',
				'description' => tra('Can download files'),
				'level' => 'basic',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_edit_gallery_file',
				'description' => tra('Can edit a gallery file'),
				'level' => 'editors',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_list_file_galleries',
				'description' => tra('Can list file galleries'),
				'level' => 'basic',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_upload_files',
				'description' => tra('Can upload files'),
				'level' => 'registered',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_remove_files',
				'description' => tra('Can remove files'),
				'level' => 'registered',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_fgal_explorer',
				'description' => tra('Can view file galleries explorer'),
				'level' => 'basic',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_fgal_path',
				'description' => tra('Can view file galleries path'),
				'level' => 'basic',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_file_gallery',
				'description' => tra('Can view file galleries'),
				'level' => 'basic',
				'type' => 'file galleries',
				'admin' => false,
				'prefs' => array('feature_file_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_forum',
				'description' => tra('Can admin forums'),
				'level' => 'editors',
				'type' => 'forums',
				'admin' => true,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_forum_attach',
				'description' => tra('Can attach to forum posts'),
				'level' => 'registered',
				'type' => 'forums',
				'admin' => false,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_forum_autoapp',
				'description' => tra('Auto approve forum posts'),
				'level' => 'editors',
				'type' => 'forums',
				'admin' => false,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_forum_edit_own_posts',
				'description' => tra('Can edit own forum posts'),
				'level' => 'registered',
				'type' => 'forums',
				'admin' => false,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_forum_post',
				'description' => tra('Can post in forums'),
				'level' => 'registered',
				'type' => 'forums',
				'admin' => false,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_forum_post_topic',
				'description' => tra('Can start threads in forums'),
				'level' => 'registered',
				'type' => 'forums',
				'admin' => false,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_forum_read',
				'description' => tra('Can read forums'),
				'level' => 'basic',
				'type' => 'forums',
				'admin' => false,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_forums_report',
				'description' => tra('Can report posts to moderator'),
				'level' => 'registered',
				'type' => 'forums',
				'admin' => false,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_forum_vote',
				'description' => tra('Can vote comments in forums'),
				'level' => 'registered',
				'type' => 'forums',
				'admin' => false,
				'prefs' => array('feature_forums'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_freetags',
				'description' => tra('Can admin freetags'),
				'level' => 'admin',
				'type' => 'freetags',
				'admin' => true,
				'prefs' => array('feature_freetags'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_galleries',
				'description' => tra('Can admin Image Galleries'),
				'level' => 'editors',
				'type' => 'image galleries',
				'admin' => true,
				'prefs' => array('feature_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_assign_perm_image_gallery',
				'description' => tra('Can assign perms to image gallery'),
				'level' => 'admin',
				'type' => 'image galleries',
				'admin' => false,
				'prefs' => array('feature_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_batch_upload_image_dir',
				'description' => tra('Can use Directory Batch Load'),
				'level' => 'editors',
				'type' => 'image galleries',
				'admin' => false,
				'prefs' => array('feature_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_batch_upload_images',
				'description' => tra('Can upload zip files with images'),
				'level' => 'editors',
				'type' => 'image galleries',
				'admin' => false,
				'prefs' => array('feature_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_create_galleries',
				'description' => tra('Can create image galleries'),
				'level' => 'editors',
				'type' => 'image galleries',
				'admin' => false,
				'prefs' => array('feature_galleries'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_freetags_tag',
				'description' => tra('Can tag objects'),
				'level' => 'registered',
				'type' => 'freetags',
				'admin' => false,
				'prefs' => array('feature_freetags'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_list_image_galleries',
				'description' => tra('Can list image galleries'),
				'level' => 'basic',
				'type' => 'image galleries',
				'admin' => false,
				'prefs' => array('feature_galleries'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_unassign_freetags',
				'description' => tra('Can unassign tags from an object'),
				'level' => 'basic',
				'type' => 'freetags',
				'admin' => false,
				'prefs' => array('feature_freetags'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_upload_images',
				'description' => tra('Can upload images'),
				'level' => 'registered',
				'type' => 'image galleries',
				'admin' => false,
				'prefs' => array('feature_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_freetags',
				'description' => tra('Can browse freetags'),
				'level' => 'basic',
				'type' => 'freetags',
				'admin' => false,
				'prefs' => array('feature_freetags'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_image_gallery',
				'description' => tra('Can view image galleries'),
				'level' => 'basic',
				'type' => 'image galleries',
				'admin' => false,
				'prefs' => array('feature_galleries'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_newsletters',
				'description' => tra('Can admin newsletters'),
				'level' => 'admin',
				'type' => 'newsletters',
				'admin' => true,
				'prefs' => array('feature_newsletters'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_batch_subscribe_email',
				'description' => tra('Can subscribe many e-mails at once (requires tiki_p_subscribe email)'),
				'level' => 'editors',
				'type' => 'newsletters',
				'admin' => false,
				'prefs' => array('feature_newsletters'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_send_newsletters',
				'description' => tra('Can send newsletters'),
				'level' => 'editors',
				'type' => 'newsletters',
				'admin' => false,
				'prefs' => array('feature_newsletters'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_subscribe_email',
				'description' => tra('Can subscribe any email to newsletters'),
				'level' => 'editors',
				'type' => 'newsletters',
				'admin' => false,
				'prefs' => array('feature_newsletters'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_subscribe_newsletters',
				'description' => tra('Can subscribe to newsletters'),
				'level' => 'basic',
				'type' => 'newsletters',
				'admin' => false,
				'prefs' => array('feature_newsletters'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_newsletter',
				'description' => tra('Can view the archive of a newsletters'),
				'level' => 'basic',
				'type' => 'newsletters',
				'admin' => false,
				'prefs' => array('feature_newsletters'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_list_newsletters',
				'description' => tra('Can list newsletters'),
				'level' => 'basic',
				'type' => 'newsletters',
				'admin' => false,
				'prefs' => array('feature_newsletters'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_polls',
				'description' => tra('Can admin polls'),
				'level' => 'admin',
				'type' => 'polls',
				'admin' => true,
				'prefs' => array('feature_polls'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_poll_results',
				'description' => tra('Can view poll results'),
				'level' => 'basic',
				'type' => 'polls',
				'admin' => false,
				'prefs' => array('feature_polls'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_vote_poll',
				'description' => tra('Can vote polls'),
				'level' => 'basic',
				'type' => 'polls',
				'admin' => false,
				'prefs' => array('feature_polls'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_poll_voters',
				'description' => tra('Can view poll voters'),
				'level' => 'basic',
				'type' => 'polls',
				'admin' => false,
				'prefs' => array('feature_polls'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_toolbars',
				'description' => tra('Can admin toolbars'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_quizzes',
				'description' => tra('Can admin quizzes'),
				'level' => 'editors',
				'type' => 'quizzes',
				'admin' => true,
				'prefs' => array('feature_quizzes'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_take_quiz',
				'description' => tra('Can take quizzes'),
				'level' => 'basic',
				'type' => 'quizzes',
				'admin' => false,
				'prefs' => array('feature_quizzes'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_quiz_stats',
				'description' => tra('Can view quiz stats'),
				'level' => 'basic',
				'type' => 'quizzes',
				'admin' => false,
				'prefs' => array('feature_quizzes'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_user_results',
				'description' => tra('Can view user quiz results'),
				'level' => 'editors',
				'type' => 'quizzes',
				'admin' => false,
				'prefs' => array('feature_quizzes'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_sheet',
				'description' => tra('Can admin sheet'),
				'level' => 'admin',
				'type' => 'sheet',
				'admin' => true,
				'prefs' => array('feature_sheet'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_sheet',
				'description' => tra('Can create and edit sheets'),
				'level' => 'editors',
				'type' => 'sheet',
				'admin' => false,
				'prefs' => array('feature_sheet'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_sheet',
				'description' => tra('Can view sheet'),
				'level' => 'basic',
				'type' => 'sheet',
				'admin' => false,
				'prefs' => array('feature_sheet'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_sheet_history',
				'description' => tra('Can view sheet history'),
				'level' => 'admin',
				'type' => 'sheet',
				'admin' => false,
				'prefs' => array('feature_sheet'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_shoutbox',
				'description' => tra('Can admin shoutbox (Edit/remove messages)'),
				'level' => 'editors',
				'type' => 'shoutbox',
				'admin' => true,
				'prefs' => array('feature_shoutbox'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_post_shoutbox',
				'description' => tra('Can post messages in shoutbox'),
				'level' => 'basic',
				'type' => 'shoutbox',
				'admin' => false,
				'prefs' => array('feature_shoutbox'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_shoutbox',
				'description' => tra('Can view shoutbox'),
				'level' => 'basic',
				'type' => 'shoutbox',
				'admin' => false,
				'prefs' => array('feature_shoutbox'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_surveys',
				'description' => tra('Can admin surveys'),
				'level' => 'editors',
				'type' => 'surveys',
				'admin' => true,
				'prefs' => array('feature_surveys'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_take_survey',
				'description' => tra('Can take surveys'),
				'level' => 'basic',
				'type' => 'surveys',
				'admin' => false,
				'prefs' => array('feature_surveys'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_survey_stats',
				'description' => tra('Can view survey stats'),
				'level' => 'basic',
				'type' => 'surveys',
				'admin' => false,
				'prefs' => array('feature_surveys'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_trackers',
				'description' => tra('Can admin trackers'),
				'level' => 'editors',
				'type' => 'trackers',
				'admin' => true,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_attach_trackers',
				'description' => tra('Can attach files to tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_tracker_view_attachments',
				'description' => tra('Can view tracker items attachments and download'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_comment_tracker_items',
				'description' => tra('Can insert comments for tracker items'),
				'level' => 'basic',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_tracker_view_comments',
				'description' => tra('Can view tracker items comments'),
				'level' => 'basic',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_create_tracker_items',
				'description' => tra('Can create new items for trackers'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_list_trackers',
				'description' => tra('Can list trackers'),
				'level' => 'basic',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_modify_tracker_items',
				'description' => tra('Can change tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_modify_tracker_items_pending',
				'description' => tra('Can change pending tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_modify_tracker_items_closed',
				'description' => tra('Can change closed tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_remove_tracker_items',
				'description' => tra('Can remove tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_remove_tracker_items_pending',
				'description' => tra('Can remove pending tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_remove_tracker_items_closed',
				'description' => tra('Can remove closed tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_tracker_view_ratings',
				'description' => tra('Can view rating result for tracker items'),
				'level' => 'basic',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_tracker_vote_ratings',
				'description' => tra('Can vote a rating for tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_tracker_revote_ratings',
				'description' => tra('Can re-vote a rating for tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_trackers',
				'description' => tra('Can view trackers'),
				'level' => 'basic',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_trackers_closed',
				'description' => tra('Can view closed trackers items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view_trackers_pending',
				'description' => tra('Can view pending trackers items'),
				'level' => 'editors',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_watch_trackers',
				'description' => tra('Can watch tracker'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_export_tracker',
				'description' => tra('Can export tracker items'),
				'level' => 'registered',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_wiki',
				'description' => tra('Can admin the wiki'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => true,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_assign_perm_wiki_page',
				'description' => tra('Can assign perms to wiki pages'),
				'level' => 'admin',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_edit',
				'description' => tra('Can edit pages'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_edit_copyrights',
				'description' => tra('Can edit copyright notices'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('wiki_feature_copyrights'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_edit_dynvar',
				'description' => tra('Can edit dynamic variables'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_structures',
				'description' => tra('Can create and edit structures'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_structure'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_export_wiki',
				'description' => tra('Can export wiki pages using the export feature'),
				'level' => 'admin',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_export'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_lock',
				'description' => tra('Can lock pages'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_usrlock'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_minor',
				'description' => tra('Can save as minor edit'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('wiki_edit_minor'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_remove',
				'description' => tra('Can remove'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_rename',
				'description' => tra('Can rename pages'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_rollback',
				'description' => tra('Can rollback pages'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_upload_picture',
				'description' => tra('Can upload pictures to wiki pages'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_pictures'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_use_as_template',
				'description' => tra('Can use the page as a tracker template'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_view',
				'description' => tra('Can view page/pages'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_view_ref',
				'description' => tra('Can view in module and feed the wiki pages reference'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_watch_structure',
				'description' => tra('Can watch structure'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_structure'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_wiki_admin_attachments',
				'description' => tra('Can admin attachments on wiki pages'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_attachments'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_admin_ratings',
				'description' => tra('Can add and change ratings on wiki pages'),
				'level' => 'admin',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_ratings'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_wiki_attach_files',
				'description' => tra('Can attach files to wiki pages'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_attachments'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_view_attachments',
				'description' => tra('Can view wiki attachments and download'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_attachments'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_view_comments',
				'description' => tra('Can view wiki comments'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_comments'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_view_history',
				'description' => tra('Can view wiki history'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_history'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_view_ratings',
				'description' => tra('Can view rating of wiki pages'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_ratings'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_view_source',
				'description' => tra('Can view source of wiki pages'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_source'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_vote_ratings',
				'description' => tra('Can participate to rating of wiki pages'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki_ratings'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_wiki_view_similar',
				'description' => tra('Can view similar wiki pages'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_likePages'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_received_articles',
				'description' => tra('Can admin received articles'),
				'level' => 'editors',
				'type' => 'comm',
				'admin' => false,
				'prefs' => array('feature_comm'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_received_pages',
				'description' => tra('Can admin received pages'),
				'level' => 'editors',
				'type' => 'comm',
				'admin' => false,
				'prefs' => array('feature_comm'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_send_articles',
				'description' => tra('Can send articles to other sites'),
				'level' => 'editors',
				'type' => 'comm',
				'admin' => false,
				'prefs' => array('feature_comm'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_sendme_articles',
				'description' => tra('Can send articles to this site'),
				'level' => 'registered',
				'type' => 'comm',
				'admin' => false,
				'prefs' => array('feature_comm'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_sendme_pages',
				'description' => tra('Can send pages to this site'),
				'level' => 'registered',
				'type' => 'comm',
				'admin' => false,
				'prefs' => array('feature_comm'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_send_pages',
				'description' => tra('Can send pages to other sites'),
				'level' => 'registered',
				'type' => 'comm',
				'admin' => false,
				'prefs' => array('feature_comm'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_tikitests',
				'description' => tra('Can admin the TikiTests'),
				'level' => 'admin',
				'type' => 'tikitests',
				'admin' => false,
				'prefs' => array('feature_tikitests'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_tikitests',
				'description' => tra('Can edit TikiTests'),
				'level' => 'editors',
				'type' => 'tikitests',
				'admin' => false,
				'prefs' => array('feature_tikitests'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_play_tikitests',
				'description' => tra('Can replay the TikiTests'),
				'level' => 'registered',
				'type' => 'tikitests',
				'admin' => false,
				'prefs' => array('feature_tikitests'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_cache_bookmarks',
				'description' => tra('Can cache user bookmarks'),
				'level' => 'admin',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_user_bookmarks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_configure_modules',
				'description' => tra('Can configure modules'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_modulecontrols'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_create_bookmarks',
				'description' => tra('Can create user bookmarks'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_user_bookmarks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_minical',
				'description' => tra('Can use the mini event calendar'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_minical'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_notepad',
				'description' => tra('Can use the notepad'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_notepad'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_tasks_admin',
				'description' => tra('Can admin public tasks'),
				'level' => 'admin',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_tasks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_tasks',
				'description' => tra('Can use tasks'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_tasks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_tasks_receive',
				'description' => tra('Can receive tasks from other users'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_tasks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_tasks_send',
				'description' => tra('Can send tasks to other users'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_tasks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_userfiles',
				'description' => tra('Can upload personal files'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_userfiles'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_usermenu',
				'description' => tra('Can create items in personal menu'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array('feature_usermenu'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_broadcast_all',
				'description' => tra('Can broadcast messages to all user'),
				'level' => 'admin',
				'type' => 'messu',
				'admin' => false,
				'prefs' => array('feature_messages'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_broadcast',
				'description' => tra('Can broadcast messages to groups'),
				'level' => 'admin',
				'type' => 'messu',
				'admin' => false,
				'prefs' => array('feature_messages'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_messages',
				'description' => tra('Can use the messaging system'),
				'level' => 'registered',
				'type' => 'messu',
				'admin' => false,
				'prefs' => array('feature_messages'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_comments',
				'description' => tra('Can admin comments'),
				'level' => 'admin',
				'type' => 'comments',
				'admin' => true,
				'prefs' => array('feature_wiki_comments', 'feature_blogposts_comments', 'feature_file_galleries_comments', 'feature_image_galleries_comments', 'feature_article_comments', 'feature_faq_comments', 'feature_poll_comments', 'map_comments'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_comments',
				'description' => tra('Can edit all comments'),
				'level' => 'editors',
				'type' => 'comments',
				'admin' => false,
				'prefs' => array('feature_wiki_comments', 'feature_blogposts_comments', 'feature_file_galleries_comments', 'feature_image_galleries_comments', 'feature_article_comments', 'feature_faq_comments', 'feature_poll_comments', 'map_comments'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_post_comments',
				'description' => tra('Can post new comments'),
				'level' => 'registered',
				'type' => 'comments',
				'admin' => false,
				'prefs' => array('feature_wiki_comments', 'feature_blogposts_comments', 'feature_file_galleries_comments', 'feature_image_galleries_comments', 'feature_article_comments', 'feature_faq_comments', 'feature_poll_comments', 'map_comments'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_read_comments',
				'description' => tra('Can read comments'),
				'level' => 'basic',
				'type' => 'comments',
				'admin' => false,
				'prefs' => array('feature_wiki_comments', 'feature_blogposts_comments', 'feature_file_galleries_comments', 'feature_image_galleries_comments', 'feature_article_comments', 'feature_faq_comments', 'feature_poll_comments', 'map_comments'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_remove_comments',
				'description' => tra('Can delete comments'),
				'level' => 'editors',
				'type' => 'comments',
				'admin' => false,
				'prefs' => array('feature_wiki_comments', 'feature_blogposts_comments', 'feature_file_galleries_comments', 'feature_image_galleries_comments', 'feature_article_comments', 'feature_faq_comments', 'feature_poll_comments', 'map_comments'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_vote_comments',
				'description' => tra('Can vote comments'),
				'level' => 'registered',
				'type' => 'comments',
				'admin' => false,
				'prefs' => array('feature_wiki_comments', 'feature_blogposts_comments', 'feature_file_galleries_comments', 'feature_image_galleries_comments', 'feature_article_comments', 'feature_faq_comments', 'feature_poll_comments', 'map_comments'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_content_templates',
				'description' => tra('Can admin content templates'),
				'level' => 'admin',
				'type' => 'content templates',
				'admin' => true,
				'prefs' => array('feature_wiki_templates', 'feature_cms_templates'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_content_templates',
				'description' => tra('Can edit content templates'),
				'level' => 'editors',
				'type' => 'content templates',
				'admin' => false,
				'prefs' => array('feature_wiki_templates', 'feature_cms_templates'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_use_content_templates',
				'description' => tra('Can use content templates'),
				'level' => 'registered',
				'type' => 'content templates',
				'admin' => false,
				'prefs' => array('feature_wiki_templates', 'feature_cms_templates'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_html_pages',
				'description' => tra('Can edit HTML pages'),
				'level' => 'editors',
				'type' => 'html pages',
				'admin' => false,
				'prefs' => array('feature_html_pages'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_html_pages',
				'description' => tra('Can view HTML pages'),
				'level' => 'basic',
				'type' => 'html pages',
				'admin' => false,
				'prefs' => array('feature_html_pages'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_list_users',
				'description' => tra('Can list registered users'),
				'level' => 'registered',
				'type' => 'user',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_live_support_admin',
				'description' => tra('Admin live support system'),
				'level' => 'admin',
				'type' => 'support',
				'admin' => true,
				'prefs' => array('feature_live_support'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_live_support',
				'description' => tra('Can use live support system'),
				'level' => 'basic',
				'type' => 'support',
				'admin' => false,
				'prefs' => array('feature_live_support'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_map_create',
				'description' => tra('Can create new mapfile'),
				'level' => 'admin',
				'type' => 'maps',
				'admin' => false,
				'prefs' => array('feature_maps'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_map_delete',
				'description' => tra('Can delete mapfiles'),
				'level' => 'admin',
				'type' => 'maps',
				'admin' => false,
				'prefs' => array('feature_maps'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_map_edit',
				'description' => tra('Can edit mapfiles'),
				'level' => 'editors',
				'type' => 'maps',
				'admin' => false,
				'prefs' => array('feature_maps'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_map_view',
				'description' => tra('Can view mapfiles'),
				'level' => 'basic',
				'type' => 'maps',
				'admin' => false,
				'prefs' => array('feature_maps'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_map_view_mapfiles',
				'description' => tra('Can view contents of mapfiles'),
				'level' => 'registered',
				'type' => 'maps',
				'admin' => false,
				'prefs' => array('feature_maps'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_use_webmail',
				'description' => tra('Can use webmail'),
				'level' => 'registered',
				'type' => 'webmail',
				'admin' => false,
				'prefs' => array('feature_webmail', 'feature_contacts'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_use_group_webmail',
				'description' => tra('Can use group webmail'),
				'level' => 'registered',
				'type' => 'webmail',
				'admin' => false,
				'prefs' => array('feature_webmail', 'feature_contacts'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_group_webmail',
				'description' => tra('Can admin group webmail accounts'),
				'level' => 'registered',
				'type' => 'webmail',
				'admin' => false,
				'prefs' => array('feature_webmail', 'feature_contacts'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_use_personal_webmail',
				'description' => tra('Can use personal webmail accounts'),
				'level' => 'registered',
				'type' => 'webmail',
				'admin' => false,
				'prefs' => array('feature_webmail', 'feature_contacts'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_personal_webmail',
				'description' => tra('Can admin personal webmail accounts'),
				'level' => 'registered',
				'type' => 'webmail',
				'admin' => false,
				'prefs' => array('feature_webmail', 'feature_contacts'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_plugin_viewdetail',
				'description' => tra('Can view unapproved plugin details'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_plugin_preview',
				'description' => tra('Can execute unapproved plugin registered'),
				'level' => 'wiki',
				'type' => NULL,
				'admin' => 'feature_wiki',
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_plugin_approve',
				'description' => tra('Can approve plugin execution'),
				'level' => 'editors',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_wiki'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_trust_input',
				'description' => tra('Trust all user inputs including plugins (no security checks)'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_backlink',
				'description' => tra('View page backlinks'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_backlinks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_notifications',
				'description' => tra('Can admin mail notifications'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_invite_to_my_groups',
				'description' => tra('Can invite user to my groups'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_invite',
				'description' => tra('Can invite users by email, and include them in groups'),
				'level' => 'registered',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_invite'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_delete_account',
				'description' => tra('Can delete his/her own account'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_importer',
				'description' => tra('Can use the importer'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_categories',
				'description' => tra('Can admin categories'),
				'level' => 'editors',
				'type' => 'category',
				'admin' => true,
				'prefs' => array('feature_categories'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_category',
				'description' => tra('Can see the category in a listing'),
				'level' => 'basic',
				'type' => 'category',
				'admin' => false,
				'prefs' => array('feature_categories'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_modify_object_categories',
				'description' => tra('Can change the categories of the object'),
				'level' => 'editors',
				'type' => 'tiki',
				'admin' => false,
				'prefs' => array('feature_categories'),
				'scope' => 'object',
				'apply_to' => array('wiki', 'trackers'),
			),
			array(
				'name' => 'tiki_p_add_object',
				'description' => tra('Can add objects to the category (needs tiki_p_modify_object_categories)'),
				'level' => 'editors',
				'type' => 'category',
				'admin' => false,
				'prefs' => array('feature_categories'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_remove_object',
				'description' => tra('Can remove objects from the category (needs tiki_p_modify_object_categories)'),
				'level' => 'editors',
				'type' => 'category',
				'admin' => false,
				'prefs' => array('feature_categories'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_create_category',
				'description' => tra('Can create new categories'),
				'level' => 'admin',
				'type' => 'category',
				'admin' => false,
				'prefs' => array('feature_categories'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_perspective_view',
				'description' => tra('Can view the perspective'),
				'level' => 'basic',
				'type' => 'perspective',
				'admin' => false,
				'prefs' => array('feature_perspective'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_perspective_edit',
				'description' => tra('Can edit the perspective'),
				'level' => 'basic',
				'type' => 'perspective',
				'admin' => false,
				'prefs' => array('feature_perspective'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_perspective_create',
				'description' => tra('Can create a perspective'),
				'level' => 'basic',
				'type' => 'perspective',
				'admin' => false,
				'prefs' => array('feature_perspective'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_perspective_admin',
				'description' => tra('Can admin perspectives'),
				'level' => 'admin',
				'type' => 'perspective',
				'admin' => true,
				'prefs' => array('feature_perspective'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_group_view',
				'description' => tra('Can view the group'),
				'level' => 'basic',
				'type' => 'group',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_group_view_members',
				'description' => tra('Can view the group members'),
				'level' => 'basic',
				'type' => 'group',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_group_add_member',
				'description' => tra('Can add group members'),
				'level' => 'admin',
				'type' => 'group',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_group_remove_member',
				'description' => tra('Can remove group members'),
				'level' => 'admin',
				'type' => 'group',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_group_join',
				'description' => tra('Can join or leave the group'),
				'level' => 'admin',
				'type' => 'group',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_trigger_transition',
				'description' => tra('Can trigger the transition between two states'),
				'level' => 'admin',
				'type' => 'transition',
				'admin' => false,
				'prefs' => array('feature_group_transition', 'feature_category_transition'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_kaltura',
				'description' => tra('Can admin kaltura feature'),
				'level' => 'admin',
				'type' => 'kaltura',
				'admin' => true,
				'prefs' => array('feature_kaltura'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_upload_videos',
				'description' => tra('Can upload video on kaltura server'),
				'level' => 'editors',
				'type' => 'kaltura',
				'admin' => false,
				'prefs' => array('feature_kaltura'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_edit_videos',
				'description' => tra('Can edit information of kaltura entry'),
				'level' => 'editors',
				'type' => 'kaltura',
				'admin' => false,
				'prefs' => array('feature_kaltura'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_remix_videos',
				'description' => tra('Can create kaltura remix video'),
				'level' => 'editors',
				'type' => 'kaltura',
				'admin' => false,
				'prefs' => array('feature_kaltura'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_delete_videos',
				'description' => tra('Can delete kaltura entry'),
				'level' => 'editors',
				'type' => 'kaltura',
				'admin' => false,
				'prefs' => array('feature_kaltura'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_download_videos',
				'description' => tra('Can download kaltura entry'),
				'level' => 'registered',
				'type' => 'kaltura',
				'admin' => false,
				'prefs' => array('feature_kaltura'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_list_videos',
				'description' => tra('Can list kaltura entries'),
				'level' => 'basic',
				'type' => 'kaltura',
				'admin' => false,
				'prefs' => array('feature_kaltura'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_view_videos',
				'description' => tra('Can view kaltura entry'),
				'level' => 'basic',
				'type' => 'kaltura',
				'admin' => false,
				'prefs' => array('feature_kaltura'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_dsn_query',
				'description' => tra('Can execute arbitrary queries on a given DSN'),
				'level' => 'admin',
				'type' => 'dsn',
				'admin' => false,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_payment_admin',
				'description' => tra('Can administer payments'),
				'level' => 'admin',
				'type' => 'payment',
				'admin' => true,
				'prefs' => array('payment_feature'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_payment_view',
				'description' => tra('Can view payment requests and details'),
				'level' => 'admin',
				'type' => 'payment',
				'admin' => false,
				'prefs' => array('payment_feature'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_payment_manual',
				'description' => tra('Can enter manual payments'),
				'level' => 'admin',
				'type' => 'payment',
				'admin' => false,
				'prefs' => array('payment_feature'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_payment_request',
				'description' => tra('Can request a payment'),
				'level' => 'admin',
				'type' => 'payment',
				'admin' => false,
				'prefs' => array('payment_feature'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_admin_modules',
				'description' => tra('User can administer modules'),
				'level' => 'admin',
				'type' => 'tiki',
				'admin' => true,
				'prefs' => array(),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_tracker_dump',
				'description' => tra('Can save a .CSV backup of entire tracekrs'),
				'level' => 'admin',
				'type' => 'trackers',
				'admin' => false,
				'prefs' => array('feature_trackers'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_bigbluebutton_join',
				'description' => tra('Can join a meeting'),
				'level' => 'basic',
				'type' => 'bigbluebutton',
				'admin' => false,
				'prefs' => array('bigbluebutton_feature'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_bigbluebutton_moderate',
				'description' => tra('Can moderate a meeting'),
				'level' => 'admin',
				'type' => 'bigbluebutton',
				'admin' => false,
				'prefs' => array('bigbluebutton_feature'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_bigbluebutton_create',
				'description' => tra('Can create a meeting'),
				'level' => 'admin',
				'type' => 'bigbluebutton',
				'admin' => false,
				'prefs' => array('bigbluebutton_feature'),
				'scope' => 'object',
			),
			array(
				'name' => 'tiki_p_socialnetworks',
				'description' => tra('user can use social network integration'),
				'level' => 'registered',
				'type' => 'socialnetworks',
				'admin' => false,
				'prefs' => array('feature_socialnetworks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_admin_socialnetworks',
				'description' => tra('user can register this site with social networks'),
				'level' => 'admin',
				'type' => 'socialnetworks',
				'admin' => true,
				'prefs' => array('feature_socialnetworks'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_page_contribution_view',
				'description' => tra('Can view contributions to a page'),
				'level' => 'basic',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('feature_page_contribution'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_acct_create_book',
				'description' => tra('Can create/close a book'),
				'level' => 'admin',
				'type' => 'accounting',
				'admin' => true,
				'prefs' => array('feature_accounting'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_acct_manage_accounts',
				'description' => tra('Can create/edit/lock accounts'),
				'level' => 'admin',
				'type' => 'accounting',
				'admin' => true,
				'prefs' => array('feature_accounting' ),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_acct_book',
				'description' => tra('Create a new transaction'),
				'level' => 'editor',
				'type' => 'accounting',
				'admin' => false,
				'prefs' => array('feature_accounting'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_acct_view',
				'description' => tra('Permission to view the journal'),
				'level' => 'registered',
				'type' => 'accounting',
				'admin' => false,
				'prefs' => array('feature_accounting' ),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_acct_book_stack',
				'description' => tra('Can book into the stack where statements can be changed'),
				'level' => 'editor',
				'type' => 'accounting',
				'admin' => false,
				'prefs' => array('feature_accounting'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_acct_book_import',
				'description' => tra('Can import statements from external accounts'),
				'level' => 'editor',
				'type' => 'accounting',
				'admin' => false,
				'prefs' => array('feature_accounting' ),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_acct_manage_template',
				'description' => tra('Can manage templates for recurring transactions'),
				'level' => 'editor',
				'type' => 'accounting',
				'admin' => false,
				'prefs' => array('feature_accounting'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_wiki_view_latest',
				'description' => tra('Can view unapproved revisions of pages'),
				'level' => 'registered',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('flaggedrev_approval'),
				'scope' => 'global',
			),
			array(
				'name' => 'tiki_p_wiki_approve',
				'description' => tra('Can approve revisions of pages'),
				'level' => 'editor',
				'type' => 'wiki',
				'admin' => false,
				'prefs' => array('flaggedrev_approval'),
				'scope' => 'global',
			),
		);

		$cachelib->cacheItem('rawpermissions' . $prefs['language'], serialize($permissions));
		return $permissions;
	}

	function get_permissions($offset = 0, $maxRecords = -1, $sort_mode = 'permName_asc', $find = '', $type = '', $group = '', $enabledOnly = false) {
		global $prefs;

		if ($enabledOnly) {
			$raw = $this->get_enabled_permissions();
		} else {
			$raw = $this->get_raw_permissions();
		}

		$ret = array();

		foreach ($raw as $permission) {
			if ($find && stripos($permission['name'], $find) === false) {
				continue;
			}

			if ($type === 'global') {
				$ret[] = $this->permission_compatibility($permission);
			} elseif ($type == $permission['type'] && $permission['scope'] == 'object') {
				$ret[] = $this->permission_compatibility($permission);
			} elseif ($type == 'category' && $permission['scope'] != 'global') {
				$ret[] = $this->permission_compatibility($permission);
			} elseif ($permission['scope'] == 'object' && isset($permission['apply_to']) && in_array($type, $permission['apply_to'])) {
				$ret[] = $this->permission_compatibility($permission);
			}
		}

		if ($group) {
			if (is_string($group)) {
				foreach ( $ret as &$res ) {
					if ($this->group_has_permission($group, $res['permName'])) {
						$res['hasPerm'] = 'y';
						$res[count($res)/2] = 'y';	// keep indexed key too
					} else {
						$res['hasPerm'] = 'n';
						$res[count($res)/2] = 'n';
					}
				}
			} else if (is_array($group)) {
				foreach ( $ret as &$res ) {
					foreach( $group as $groupName) {
						if ($this->group_has_permission($groupName, $res['permName'])) {
							$res[$groupName.'_hasPerm'] = 'y';
							$res[count($res)/2] = 'y';
						} else {
							$res[$groupName.'_hasPerm'] = 'n';
							$res[count($res)/2] = 'n';
						}
					}
				}
			}
		}

		return array(
			'data' => $ret,
			'cant' => count($ret),
		);
	}

	private function permission_compatibility($newFormat) {
		$newFormat['permName'] = $newFormat['name'];
		$newFormat['permDesc'] = $newFormat['description'];
		$newFormat['feature_checks'] = implode(',', $newFormat['prefs']);

		return $newFormat;
	}

	function get_permission_types() {
		$ret = array();

		foreach ($this->get_raw_permissions() as $perm) {
			if (! isset($ret[$perm['type']])) {
				$ret[$perm['type']] = true;
			}
		}

		return array_keys($ret);
	}

	function get_group_permissions($group) {
		global $cachelib;
		if ( ! $ret = $cachelib->getSerialized("groupperms_$group") ) {

			$query = "select `permName` from `users_grouppermissions` where `groupName`=?";
			$result = $this->query($query, array($group));
			$ret = array();

			while ( $res = $result->fetchRow() ) {
				$ret[] = $res["permName"];
			}

			$cachelib->cacheItem("groupperms_$group",serialize($ret));
		}

		return $ret;
	}

	function assign_permission_to_group($perm, $group) {
		$query = "delete from `users_grouppermissions` where `groupName` = ? and `permName` = ?";
		$result = $this->query($query, array($group, $perm));
		$query = "insert into `users_grouppermissions`(`groupName`, `permName`) values(?, ?)";
		$result = $this->query($query, array($group, $perm));

		global $cachelib;
		$cachelib->empty_type_cache("fgals_perms");
		$cachelib->invalidate("groupperms_$group");

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->empty_menu_cache();

		return true;
	}

	function get_user_permissions($user) {
		$groups = $this->get_user_groups($user);

		$ret = array();
		foreach ($groups as $group) {
			$perms = $this->get_group_permissions($group);

			foreach ($perms as $perm) {
				$ret[] = $perm;
			}
		}

		return $ret;
	}

	function user_has_permission($user, $perm) {

		// Get user_groups ?
		$groups = $this->get_user_groups($user);

		foreach ($groups as $group) {
			if ($this->group_has_permission($group, $perm) || $this->group_has_permission($group, 'tiki_p_admin')) {
				return true;
			}
		}

		return false;
	}

	function group_has_permission($group, $perm) {
		if ( empty($perm) || empty($group) ) return 0;

		$engroup = urlencode($group);
		if ( ! isset($this->groupperm_cache[$engroup]) ) {
			$this->groupperm_cache[$engroup] = array();
			$groupperms = $this->get_group_permissions($group);
			foreach ( $groupperms as $gp ) {
				$this->groupperm_cache[$engroup][$gp] = 1;
			}
		}

		return isset($this->groupperm_cache[$engroup][$perm]) ? 1 : 0;
	}

	function remove_permission_from_group($perm, $group) {
		$query = "delete from `users_grouppermissions` where `permName` = ? and `groupName` = ?";
		$result = $this->query($query, array($perm, $group));

		global $cachelib;
		$cachelib->empty_type_cache("fgals_perms");
		$cachelib->invalidate("groupperms_$group");

		global $menulib; include_once('lib/menubuilder/menulib.php');
		$menulib->empty_menu_cache();

		return true;
	}

	function get_group_info($group, $sort_mode='groupName_asc') {
		$ret = array();
		if (is_array($group)) {
			$query = 'select * from `users_groups` where `groupName` in ('.implode(',',array_fill(0,count($group),'?')).') order by '.$this->convertSortMode($sort_mode);
			$ret = $this->fetchAll($query, $group);
		} else {
			$query = 'select * from `users_groups` where `groupName`=?';
			$result = $this->query($query, array($group));
			$ret = $result->fetchRow();
			$perms = $this->get_group_permissions($group);
			$ret['perms'] = $perms;
		}
		return $ret;
	} 

	function get_groupId_info($groupId) {
		$query = "select * from `users_groups` where `id`=?";

		$result = $this->query($query, array($groupId));
		$res = $result->fetchRow();
		$perms = $this->get_group_permissions($res['groupName']);
		$res["perms"] = $perms;
		return $res;
	}

	function assign_user_to_group($user, $group) {
		global $cachelib; require_once("lib/cache/cachelib.php");
		global $tikilib, $prefs;
		$cachelib->invalidate('user_details_'.$user);
		$tikilib->invalidate_usergroups_cache($user);

		$group_ret = false;
		$userid = $this->get_user_id($user);

		if ( $userid > 0 ){
			$query = "insert into `users_usergroups`(`userId`,`groupName`, `created`) values(?,?,?)";
			$result = $this->query($query, array($userid, $group, $tikilib->now), -1, -1, false);
			$group_ret = true;
			if ($prefs['user_trackersync_groups'] == 'y') {
				$this->categorize_user_tracker_item($user, $group);	
			}
		}
		return $group_ret;
	}

	function assign_user_to_groups($user, $groups) {
		global $cachelib;
		require_once("lib/cache/cachelib.php");
		$cachelib->invalidate('user_details_'.$user);

		$userid = $this->get_user_id($user);

		$query = "delete from `users_usergroups` where `userId`=?";
		$this->query($query, array($userid));

		foreach ($groups as $grp) {
			$this->assign_user_to_group($user, $grp);
		}

	}

	function hash_pass($pass, $salt = NULL) {
		global $prefs;

		$hashmethod=$prefs['feature_crypt_passwords'];

		if (!is_null($salt)) {
			$len=strlen($salt);
			if ($len == 13) { // CRYPT_STD_DES
				$hashmethod='crypt-des';
			} else if ($len == 34) { // CRYPT_MD5
				$hashmethod='crypt-md5';
			} else if ($len == 32) { // md5()
				$hashmethod='tikihash';
			} else if ($len == 0) { // password is disabled in tiki -> external authentification
				$hashmethod='pass_disabled';
			} else {
				die("Unknown password format");
			}
		}

		switch($hashmethod) {

			case 'crypt':
				return crypt($pass);

			case 'crypt-des':
				if (CRYPT_STD_DES != 1) die("CRYPT_STD_DES not implemented on this system");
				if (is_null($salt)) {
					$letters="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./";
					$salt='';
					for ($i=0; $i<2; $i++) $salt.=$letters[rand(0, strlen($letters) - 1)];
				}
				return crypt($pass, $salt);

			case 'crypt-md5':
				if (CRYPT_MD5 != 1) die("CRYPT_MD5 not implemented on this system");
				if (is_null($salt)) {
					$letters="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789./";
					$salt='$1$';
					for ($i=0; $i<8; $i++) $salt.=$letters[rand(0, strlen($letters) - 1)];
					$salt.='$';
				}
				return crypt($pass, $salt);

			case 'pass_disabled': // md5(someting) is not empty string
			case 'tikihash':
			default:
				return md5($pass);
		}
	}

	function confirm_user($user) {
		global $prefs,$cachelib;

		$query = "update `users_users` set `provpass`=?, valid=?, `email_confirm`=?, `waiting`=? where `login`=?";
		$result = $this->query($query, array('', NULL, $this->now, NULL, $user));
		$cachelib->invalidate('userslist');
	}

	function invalidate_account($user) {
		global $prefs,$cachelib, $tikilib;

		$query = "update `users_users` set valid=?, `waiting`=? where `login`=?";
		$result = $this->query($query, array(md5($tikilib->genPass()), 'u', $user));
		$cachelib->invalidate('userslist');
	}

	function change_user_waiting($user, $who) {
		$query = 'update `users_users` set `waiting`=? where `login`=?';
		$this->query($query, array($who, $user));
	}

	function add_user($user, $pass, $email, $provpass = '', $pass_first_login = false, $valid = NULL, $openid_url = NULL, $waiting=NULL) {
		global $tikilib, $cachelib, $prefs;

		if ($this->user_exists($user) || empty($user) || (!empty($prefs['username_pattern']) && !preg_match($prefs['username_pattern'], $user)) || strtolower($user) == 'anonymous' || strtolower($user) == 'registered') {
			return false;
		}

		// Generate a unique hash; this is also done below in set_user_fields()
		$lastLogin = null;
		if (empty($openid_url))
		{
			$hash = $this->hash_pass($pass);
		}
		else
		{
			$hash = '';
			if (!isset($prefs['validateRegistration']) || $prefs['validateRegistration'] != 'y')
				$lastLogin = $tikilib->now;
		}

		if ( $prefs['feature_clear_passwords'] == 'n' ) {
			$pass = '';
		}

		if ( $pass_first_login ) {
			$new_pass_confirm = 0;
		} else {
			$new_pass_confirm = $this->now;
		}
		$new_email_confirm = $this->now;
		$query = "insert into
			`users_users`(`login`, `password`, `email`, `provpass`,
					`registrationDate`, `hash`, `pass_confirm`, `email_confirm`, `created`, `valid`, `openid_url`, `lastLogin`, `waiting`)
			values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query, array(
			$user,
			$pass,
			$email,
			$provpass,
			(int) $this->now,
			$hash,
			(int) $new_pass_confirm,
			(int) $new_email_confirm,
			(int) $this->now,
			$valid,
			$openid_url,
			$lastLogin,
			//(empty($_GLOBALS['user']) && $prefs['validateRegistration'] == 'y')? 'a': ((empty($_GLOBALS['user']) && $prefs['validateUsers'] == 'y')? 'u': NULL)
			$waiting
		));

		$this->assign_user_to_group($user, 'Registered');

		if( $prefs['eponymousGroups'] == 'y' )
		{
			// Create a group just for this user, for permissions
			// assignment.
			$this->add_group($user, "Personal group for $user.",'',0,0,0,'');

			$this->assign_user_to_group($user, $user);
		}

		$this->set_user_default_preferences($user, false); // do not force

		$cachelib->invalidate('userslist');
		return true;
	}

	function set_user_default_preferences($user, $force=true) {
		global $prefs;
		foreach( $prefs as $pref => $value ) {
			if ( ! preg_match('/^users_prefs_/', $pref) ) continue;
			if ($pref == 'users_prefs_email_is_public') {
				$pref_name = 'email is public';
			} else {
				$pref_name = substr( $pref, 12 );
			}
			if ($force || is_null($this->get_user_preference($user, $pref_name))) {
				$this->set_user_preference($user, $pref_name, $value);
			}
		}
	}
	function change_user_email_only($user, $email) {
		$query = 'update `users_users` set `email`=? where binary `login`=?';
		$result = $this->query($query, array($email, $user));
	}

	function change_user_email($user, $email, $pass=null) {
		// Need to change the email-address for notifications, too
		global $notificationlib; include_once('lib/notifications/notificationlib.php');
		$oldMail = $this->get_user_email($user);
		$notificationlib->update_mail_address($user, $oldMail, $email);

		$this->change_user_email_only($user, $email);

		// that block stays here for a time (compatibility)
		// lfagundes - only if pass is provided, admin doesn't need it
		// is this still necessary?
		if (!empty($pass)) {
			$hash = $this->hash_pass($pass);
			$query = "update `users_users` set `hash`=? where binary `login`=?";
			$result = $this->query($query, array(
				$hash,
				$user
			));
		}

		$query = "update `tiki_user_watches` set `email`=? where binary `user`=?";
		$result = $this->query($query, array( $email, $user));

		$query = "update `tiki_live_support_requests` set `email`=? where binary `user`=?";
		$result = $this->query($query, array( $email, $user));
		return true;
	}

	function get_user_password($user) {
		$query = "select `password`,`provpass` from `users_users` where binary `login`=?";

		$result = $this->query($query, array($user));
		$res = $result->fetchRow();
		if (empty($res['provpass']))
			return $res['password'];
		else
			return $res['provpass'];
	}

	function get_user_email($user) {
		global $prefs;
		return ( $prefs['login_is_email'] == 'y' && $user != 'admin' ) ? $user : $this->getOne("select `email` from `users_users` where binary `login`=?", array($user));
	}
	function get_userId_what($userIds, $what='email') {
		$query = "select `$what` from `users_users` where `userId` in (".implode(',',array_fill(0, count($userIds),'?')).')';
		$result = $this->query($query, $userIds);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res[$what];
		}
		return $ret;
	}

	/**
	 * Returns the contact users' email if set and permitted by Admin->Features settings
	 */
	function get_admin_email() {
		global $user, $prefs, $tikilib;
		if (( !isset($user) && isset($prefs['contact_anon']) && $prefs['contact_anon'] == 'y' ) ||
				( isset($user) && $user != '' && isset($prefs['feature_contact']) && $prefs['feature_contact'] == 'y' )) {
			return isset($prefs['sender_email']) ? $prefs['sender_email'] : $this->get_user_email($prefs['contact_user']);
		}
	}

	function create_user_cookie($user, $secret=false) {
		global $prefs;
		if (!$secret) {
			$secret = $this->get_cookie_check();
		}
		$this->delete_user_cookie($user);

		$query = "insert into `tiki_user_login_cookies`(`userId`, `secret`, `expiration`) values(?, ?, FROM_UNIXTIME(?))";
		$result = $this->query($query, array($user, $secret, $this->now + $prefs['remembertime']));
		return $secret;
	}

	function delete_user_cookie($user) {
		$query = 'delete from `tiki_user_login_cookies` where `userId`=?';
		$this->query($query, array($user));
	}

	function get_cookie_check() {
		return md5(session_id() . uniqid(mt_rand(), true));
	}

	function get_user_by_cookie($cookie) {
		global $prefs;
		list($secret, $userId) = explode('.', $cookie, 2);
		$query = "select `userId` from `tiki_user_login_cookies` where `secret`=? and `userId`=? and `expiration` > NOW()";

		if ($userId === $this->getOne($query, array($secret, $userId))) {
			return $userId;
		}
		return false;
	}

	function get_user_by_email($email) {
		$query = "select `login` from `users_users` where `email`=?";
		$pass = $this->getOne($query, array($email));
		return $pass;
	}

	function is_due($user) {
		global $prefs;
		// if CAS auth is enabled, don't check if password is due since CAS does not use local Tiki passwords
		if ( $prefs['auth_method'] == 'cas' || $prefs['change_password'] != 'y') {
			return false;
		}
		$confirm = $this->getOne("select `pass_confirm` from `users_users` where binary `login`=?", array($user));
		if (!$confirm) {
			return true;
		}
		if ($prefs['pass_due'] < 0) {
			return false;
		}
		if ($confirm + (60 * 60 * 24 * $prefs['pass_due']) < $this->now) {
			return true;
		}
		return false;
	}

	function is_email_due($user) {
		global $prefs;
		if ($prefs['email_due'] < 0) {
			return false;
		}
		$confirm = $this->getOne("select `email_confirm` from `users_users` where binary `login`=?", array($user));
		if ($confirm + (60 * 60 * 24 * $prefs['email_due']) < $this->now) {
			return true;
		}
		return false;
	}

	function unsuccessful_logins($user) {
		return $this->getOne('select `unsuccessful_logins` from `users_users` where binary `login`=?', array($user));
	}

	function renew_user_password($user) {
		$pass = $this->genPass();
		// Note that tiki-generated passwords are due inmediatley
		// Note: ^ not anymore. old pw is usable until the URL in the password reminder mail is clicked
		$query = "update `users_users` set `provpass` = ? where `login`=?";
		$result = $this->query($query, array($pass, $user));
		return $pass;
	}

	function activate_password($user, $actpass) {
		// move provpass to password and generate new hash, afterwards clean provpass
		$query = "select `provpass` from `users_users` where `login`=?";
		$pass = $this->getOne($query, array($user));
		if (($pass <> '') && ($actpass == md5($pass))) {
			$hash = $this->hash_pass($pass);
			$query = "update `users_users` set `password`=?, `hash`=?, `pass_confirm`=? where `login`=?";
			$result = $this->query($query, array("", $hash, (int)$this->now, $user));
			return $pass;
		}
		return false;
	}

	/* Tests the password against policy enforcement (Admin->Login), namelly:
	* $min_pass_length
	* $pass_chr_num
	* $pass_ud_chr_num
	*
	* returns an empty string if password is ok, or the error string otherwise
	*/
	function check_password_policy($pass) {
		global $prefs, $user;
		$errors = array();

		// Validate password here
		if ( ( $prefs['auth_method'] != 'cas' || $user == 'admin' ) && strlen($pass) < $prefs['min_pass_length'] ) {
			$errors[] = tra("Password should be at least").' '.$prefs['min_pass_length'].' '.tra("characters long");
		}

		// Check this code
		if ($prefs['pass_chr_num'] == 'y') {
			if (!preg_match_all("/[0-9]+/", $pass, $foo) || !preg_match_all("/[A-Za-z]+/", $pass, $foo)) {
				$errors[] = tra("Password must contain both letters and numbers");
			}
		}
		if ($prefs['pass_chr_case'] == 'y') {
			if (!preg_match_all("/[a-z]+/", $pass, $foo) || !preg_match_all("/[A-Z]+/", $pass, $foo)) {
				$errors[] = tra('Password must contain at least one alphabetical character in lower case like a and one in upper case like A.');
			}
		}
		if ($prefs['pass_chr_special'] == 'y') {
			$chars = str_split($pass);
			$ok = false;
			foreach ($chars as $char) {
				if (!preg_match("/[0-9A-Za-z]+/", $char, $foo)) {
					$ok = true;
					break;
				}
			}
			if (!$ok) $errors[] = tra('Password must contain at least one special character in lower case like " / $ % ? & * ( ) _ + ...');
		}
		if ($prefs['pass_repetition'] == 'y') {
			$chars = str_split($pass);
			$previous = '';
			foreach ($chars as $char) {
				if ($char == $previous) {
					$errors[] = tra('Password must contain no consecutive repetition of the same character as 111 or aab');
					break;
				}
				$previous = $char;
			}
		}
		if ($prefs['pass_diff_username'] == 'y') {
			if (strtolower($user) == strtolower($pass)) {
				$errors[] = tra('Password must be different from the user login.');
			}
		}

		return empty($errors)?'': implode(' ', $errors);
	}

	function change_user_password($user, $pass, $pass_first_login=false) {
		global $prefs;

		$hash = $this->hash_pass($pass);
		$new_pass_confirm = $this->now;
		$provpass = $pass;

		if ($prefs['feature_clear_passwords'] == 'n') {
			$pass = '';
		}

		if ($pass_first_login) {
			if (!empty($provpass)) {
				$query = 'update `users_users` set `hash`=? ,`password`=? ,`pass_confirm`=?, `provpass`=?, `pass_confirm`=? where binary `login`=?';
				$this->query($query, array($hash, $pass, $new_pass_confirm, $provpass, 0, $user));
			} else {
				$query = 'update `users_users` set `pass_confirm`=? where binary `login`=?';
				$this->query($query, array(0, $user));
			}
		} else {
			$query = "update `users_users` set `hash`=? ,`password`=? ,`pass_confirm`=?, `provpass`=? where binary `login`=?";
			$this->query($query, array($hash, $pass, $new_pass_confirm, '',	$user));
		}
		// invalidate the cache so that after a fresh install, the admin (who has no user details at the install) can log in
		global $cachelib; require_once('lib/cache/cachelib.php');
		$cachelib->invalidate('user_details_'.$user);
		return true;
	}

	function add_group($group, $desc='', $home='', $utracker=0, $gtracker=0, $rufields='', $userChoice='', $defcat=0, $theme='', $ufield=0, $gfield=0,$isexternal='n', $expireAfter=0, $emailPattern='') {
		global $tikilib;
		$group = trim($group);
		if ( $this->group_exists($group) ) return false;

		$query = "insert into `users_groups` (`groupName`, `groupDesc`, `groupHome`,`groupDefCat`,`groupTheme`,`usersTrackerId`,`groupTrackerId`, `registrationUsersFieldIds`, `userChoice`, `usersFieldId`, `groupFieldId`,`isExternal`, `expireAfter`, `emailPattern`) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->query($query, array($group, $desc, $home, $defcat, $theme, (int)$utracker, (int)$gtracker, $rufields, $userChoice, (int)$ufield, (int)$gfield,$isexternal, $expireAfter, $emailPattern) );

		global $cachelib; require_once('lib/cache/cachelib.php');
		$cachelib->invalidate('grouplist');
		$cachelib->invalidate('groupIdlist');

		$query = "select `id` from `users_groups` where `groupName`=?";
		return $this->getOne($query, array($group));
	}

	function change_group($olgroup,$group,$desc,$home,$utracker=0,$gtracker=0,$ufield=0,$gfield=0,$rufields='',$userChoice='',$defcat=0,$theme='',$isexternal='n', $expireAfter=0, $emailPattern='') {

		if ( $olgroup == 'Anonymous' || $olgroup == 'Registered' ) {
			// Changing group name of 'Anonymous' and 'Registered' is not allowed.
			if ( $group != $olgroup ) return false;
		}

		if ( ! $this->group_exists($olgroup) ) {
			return $this->add_group($group, $desc, $home, $utracker,$gtracker, $userChoice, $defcat, $theme, $isExternal, $expireAfter, $emailPattern);
		}

		global $cachelib;

		$query = "update `users_groups` set `groupName`=?, `groupDesc`=?, `groupHome`=?, `groupDefCat`=?, `groupTheme`=?, `usersTrackerId`=?, `groupTrackerId`=?, `usersFieldId`=?, `groupFieldId`=? , `registrationUsersFieldIds`=?, `userChoice`=?, `isExternal`=?, `expireAfter`=?, `emailPattern`=? where `groupName`=?";
		$result = $this->query($query, array($group, $desc, $home, $defcat, $theme, (int)$utracker, (int)$gtracker, (int)$ufield, (int)$gfield, $rufields, $userChoice, $isexternal, $expireAfter, $emailPattern, $olgroup));

		if ( $olgroup != $group ) {
			$query = array();
			$query[] = "update `users_usergroups` set `groupName`=? where `groupName`=?";
			$query[] = "update `users_grouppermissions` set `groupName`=? where `groupName`=?";
			$query[] = "update `users_objectpermissions` set `groupName`=? where `groupName`=?";
			$query[] = "update `tiki_group_inclusion` set `groupName`=? where `groupName`=?";
			$query[] = "update `tiki_group_inclusion` set `includeGroup`=? where `includeGroup`=?";
			$query[] = "update `tiki_newsletter_groups` set `groupName`=? where `groupName`=?";
			foreach ( $query as $q ) $this->query($q, array($group, $olgroup));

			// must unserialize before replacing the groups
			$query = "select `name`, `groups` from `tiki_modules` where `groups` like ?";
			$result = $this->query($query, array('%'.$olgroup.'%'));
			while ( $res = $result->fetchRow() ) {
				$aux = array();
				$aux["name"] = $res["name"];
				$aux["groups"] = unserialize($res["groups"]);
				$aux["groups"] = str_replace($olgroup, $group, $aux["groups"]);
				$aux["groups"] = serialize($aux["groups"]);
				$query = "update `tiki_modules` set `groups`=? where `name`=?";
				$this->query($query, array($aux["groups"], $aux["name"]));
			}

			$query = 'select * from `tiki_tracker_fields` where `visibleBy` like ?';
			$result = $this->query($query, array('%"'.$olgroup.'"%'));
			$query = 'update `tiki_tracker_fields` set `visibleBy`=? where `visibleBy`=?';
			while ( $res = $result->fetchRow() ) {
				$g = unserialize($res['visibleBy']);
				$g = str_replace($olgroup, $group, $g);
				$g = serialize($g);
				$this->query($query, array($g, $res['visibleBy']));
			}

			$query = 'select * from `tiki_tracker_fields` where `editableBy` like ?';
			$result = $this->query($query, array('%"'.$olgroup.'"%'));

			$query = 'update `tiki_tracker_fields` set `editableBy`=? where `editableBy`=?';
			while ( $res = $result->fetchRow() ) {
				$g = unserialize($res['editableBy']);
				$g = str_replace($olgroup, $group, $g);
				$g = serialize($g);
				$this->query($query, array($g, $res['editableBy']));
			}

			$cachelib->invalidate('grouplist');
			$cachelib->invalidate('group_theme_'.$group);
		}
		$cachelib->invalidate('group_theme_'.$olgroup);

		return true;
	}

	function remove_all_inclusions($group) {
		if (!$this->group_exists($group))
			return false;

		$query = "delete from `tiki_group_inclusion` where `groupName` = ?";
		$result = $this->query($query, array($group));
		global $cachelib; require_once('lib/cache/cachelib.php');
		$cachelib->empty_type_cache('group_inclusion_'.$group);
		$this->groupinclude_cache = array();
		return true;
	}

	function set_user_fields($u) {
		global $prefs;

		$q = array();
		$bindvars = array();

		if (isset($u['password'])) {
			if ($prefs['feature_clear_passwords'] == 's') {
				$q[] = "`password` = ?";
				$bindvars[] = strip_tags($u['password']);
			}

			// I don't think there are currently cases where login and email are undefined
			//$hash = md5($u['login'] . $u['password'] . $u['email']);
			$hash = $this->hash_pass($u['password']);
			$q[] = "`hash` = ?";
			$bindvars[] = $hash;
		}

		if (isset($u['email'])) {
			$q[] = "`email` = ?";
			$bindvars[] = strip_tags($u['email']);
		}

		if (isset($u['openid_url'])) {
			if (isset($_SESSION['openid_url'])) {
				$q[] = "`openid_url` = ?";
				$bindvars[] = $u['openid_url'];
			}
		}

		if (count($q) > 0) {
			$query = "update `users_users` set " . implode(",", $q). " where binary `login` = ?";
			$bindvars[] = $u['login'];
			$result = $this->query($query, $bindvars);
		}

		$aUserPrefs = array('realName','homePage','country');
		foreach ($aUserPrefs as $pref) {
			if (isset($u[$pref])) {
				$this->set_user_preference($u['login'],$pref,$u[$pref]);
			}
		}

		return $result;
	}

	function count_users($group) {
		static $rv = array();

		if (!isset($rv[$group])) {
			if ($group == '') {
				$query = "select count(login) from `users_users`";
				$result = $this->getOne($query);
			} else {
				$query = "select count(userId) from `users_usergroups` where `groupName` = ?";
				$result = $this->getOne($query, array($group));
			}
			$rv[$group] = $result;
		}

		return $rv[$group];
	}

	function related_users($user, $max=10, $type='wiki') {
		if(!isset($user) || empty($user)) {
			return array();
		}

		// This query was written using a double join for PHP. If you're trying to eke
		// additional performance and are running MySQL 4.X, you might want to try a
		// subselect and compare perf numbers.

		if ($type == 'wiki') {
			$query = "SELECT u1.`login`, COUNT( p1.`pageName` ) AS quantity
				FROM `tiki_history` p1
				INNER JOIN `users_users` u1 ON ( u1.`login` = p1.`user` )
				INNER JOIN `tiki_history` p2 ON ( p1.`pageName` = p2.`pageName` )
				INNER JOIN `users_users` u2 ON ( u2.`login` = p2.`user` )
				WHERE u2.`login` = ? AND u1.`login` <> ?
				GROUP BY p1.`pageName`
				ORDER BY quantity DESC
				";
		} else {
			return array();
		}

		$bindvals = array($user, $user);

		return $this->fetchAll($query, $bindvals, $max, 0);
	}

	// Friends methods
	function request_friendship($user, $friend) {
		if (empty($user) || empty($friend) || $user == $friend) {
			return false;
		}

		$query = "delete from `tiki_friendship_requests` where `userFrom`=? and `userTo`=?";
		$this->query($query, array($user, $friend));

		$query = "insert into `tiki_friendship_requests` (`userFrom`, `userTo`) values (?, ?)";
		$result = $this->query($query, array($user, $friend));

		if (!$result)
			return false;

		return true;
	}

	function accept_friendship($user, $friend) {
		$exists = $this->getOne("select count(*) from `tiki_friendship_requests` where `userTo`=? and `userFrom`=?",
				array($user, $friend));

		if (!$exists)
			return false;

		if (empty($user) || empty($friend)) {
			return false;
		}

		$query = "delete from `tiki_friends` where `user`=? and `friend`=?";
		$this->query($query, array($user, $friend));
		$this->query($query, array($friend, $user));

		$query = "insert into `tiki_friends` (`user`, `friend`) values (?,?)";
		$this->query($query, array($user, $friend));
		$this->query($query, array($friend, $user));

		$query = "delete from `tiki_friendship_requests` where `userFrom`=? and `userTo`=?";
		$this->query($query, array($user, $friend));
		$this->query($query, array($friend, $user));

		$this->score_event($user,'friend_new',$friend);
		$this->score_event($friend,'friend_new',$user);

		global $cachelib;
		$cachelib->invalidate('friends_count_'.$user);
		$cachelib->invalidate('friends_count_'.$friend);

		return true;
	}

	function refuse_friendship($user, $friend) {
		$exists = $this->getOne("select count(*) from `tiki_friendship_requests` where `userTo`=? and `userFrom`=?",
				array($user, $friend));

		if (!$exists)
			return false;

		$query = "delete from `tiki_friendship_requests` where `userFrom`=? and `userTo`=?";
		$this->query($query, array($user, $friend));
		$this->query($query, array($friend, $user));

		return true;
	}

	function list_pending_friendship_requests($user) {

		$query = "select * from `tiki_friendship_requests` where `userTo`=? order by tstamp";
		$result = $this->query($query, array($user));

		$requests = array();
		while ($res = $result->fetchRow('DB_FETCHMODE_ASSOC')) {
			$requests[$res['userFrom']] = $res['tstamp'];
		}

		return $requests;
	}

	function list_waiting_friendship_requests($user) {
		$query = "select * from `tiki_friendship_requests` where `userFrom`=? order by tstamp";
		$result = $this->query($query, array($user));

		$requests = array();
		while ($res = $result->fetchRow('DB_FETCHMODE_ASSOC')) {
			$requests[$res['userTo']] = $res['tstamp'];
		}

		return $requests;
	}

	function break_friendship($user, $friend) {

		$query = "delete from `tiki_friends` where `user`=? and `friend`=?";
		$this->query($query, array($user, $friend));
		$this->query($query, array($friend, $user));

		global $cachelib;
		$cachelib->invalidate('friends_count_'.$user);
		$cachelib->invalidate('friends_count_'.$friend);
	}


		// Case-sensitivity regression only. used for patching
	function get_object_case_permissions($objectId, $objectType) {
		$query = "select `groupName`, `permName` from `users_objectpermissions` where `objectId` = ? and `objectType` = ?";
		return $this->fetchAll($query, array(md5($objectType . $objectId),$objectType));
	}

	function object_has_one_case_permission($objectId, $objectType) {
		$query = "select count(*) from `users_objectpermissions` where `objectId`=? and `objectType`=?";
		$result = $this->getOne($query, array( md5($objectType . $objectId), $objectType));
		return $result;
	}

	function remove_object_case_permission($groupName, $objectId, $objectType, $permName) {
		$query = "delete from `users_objectpermissions` where `groupName` = ? and `objectId` = ? and `objectType` = ? and `permName` = ?";
		$result = $this->query($query, array($groupName, md5($objectType . $objectId), $objectType, $permName));
		return true;
	}

	function send_validation_email($name, $apass, $email, $again='', $second='', $chosenGroup='', $mailTemplate = '', $pass = '') {
		// TODO: CLEANUP duplicates code in callback_tikiwiki_send_email() in registrationlib?
		global $tikilib, $prefs, $smarty;
		$foo = parse_url($_SERVER['REQUEST_URI']);
		$foo1 = str_replace(array('tiki-send_mail', 'tiki-register', 'tiki-remind_password', 'tiki-adminusers', 'remote'), 'tiki-login_validate', $foo['path']);
		$foo2 = str_replace(array('tiki-send_mail', 'tiki-register', 'tiki-remind_password', 'tiki-adminusers'), 'tiki-assignuser', $foo['path']);
		$foo3 = str_replace(array('tiki-send_mail', 'tiki-register', 'tiki-remind_password', 'tiki-adminusers'), 'tiki-user_preferences', $foo['path']);
		$machine = $tikilib->httpPrefix( true ) . $foo1;
		$machine_assignuser = $tikilib->httpPrefix( true ) . $foo2;
		$machine_userprefs = $tikilib->httpPrefix( true ) . $foo3;
		$smarty->assign('mail_machine',$machine);
		$smarty->assign('mail_machine_assignuser',$machine_assignuser);
		$smarty->assign('mail_machine_userprefs',$machine_userprefs);
		$smarty->assign('mail_site', $_SERVER['SERVER_NAME']);
		$smarty->assign('mail_user', $name);
		$smarty->assign('mail_apass', $apass);
		$smarty->assign('mail_email', $email);
		$smarty->assign('mail_again', $again);
		include_once('lib/webmail/tikimaillib.php');
		if ($second == 'y') {
			$mail_data = $smarty->fetch('mail/confirm_user_email_after_approval.tpl');
			$mail = new TikiMail();
			$mail->setText($mail_data);
			$mail_data = sprintf($smarty->fetch('mail/confirm_user_email_after_approval_subject.tpl'), $_SERVER['SERVER_NAME']);
			$mail->setSubject($mail_data);
			if (!$mail->send(array($email))) {
				$smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
				return false;
			}
		} elseif ($prefs['validateRegistration'] == 'y' && empty($pass)) {
			if (!empty($chosenGroup)) {
				$smarty->assign_by_ref('chosenGroup', $chosenGroup);
				if ($prefs['userTracker'] == 'y') {	// this cannot work here as the tracker item needs the user to be registered to be created first - FIXME catch 22
					global $trklib; include_once('lib/trackers/trackerlib.php');
					$re = $this->get_group_info(isset($chosenGroup)? $chosenGroup: 'Registered');
					$fields = $trklib->list_tracker_fields($re['usersTrackerId'], 0, -1, 'position_asc', '', true, array('fieldId'=>explode(':',$re['registrationUsersFieldIds'])));
					$listfields = array();
					foreach ($fields['data'] as $field) {
						$listfields[$field['fieldId']] = $field;
					}
					$items = $trklib->list_items($re['usersTrackerId'], 0, 1, '', $listfields, $trklib->get_field_id_from_type($re['usersTrackerId'], 'u', '1%'), '', '', '', $name);
					if (isset($items['data'][0]))
						$smarty->assign_by_ref('item', $items['data'][0]);
				}
			}
			$mail_data = $smarty->fetch('mail/moderate_validation_mail.tpl');
			$mail_subject = $smarty->fetch('mail/moderate_validation_mail_subject.tpl');
			$emails = !empty($prefs['validator_emails'])?preg_split('/,/', $prefs['validator_emails']): (!empty($prefs['sender_email'])? array($prefs['sender_email']): '');
			if (empty($emails)) {
				if ($prefs['feature_messages'] != 'y') {
					$smarty->assign('msg', tra("The registration mail can't be sent because there is no server email address set, and this feature is disabled").": feature_messages");
					return false;
				}
				include_once('lib/messu/messulib.php');
				$messulib->post_message($prefs['contact_user'], $prefs['contact_user'], $prefs['contact_user'], '', $mail_subject, $mail_data, 5);
				$smarty->assign('msg', $smarty->fetch('mail/user_validation_waiting_msg.tpl'));
			} else {
				$mail = new TikiMail();
				$mail->setText($mail_data);
				$mail->setSubject($mail_subject);
				if (!$mail->send($emails)) {
					$smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
					return false;
				} elseif (empty($again)) {
					$smarty->assign('msg', $smarty->fetch('mail/user_validation_waiting_msg.tpl'));
				} else {
					$smarty->assign('msg', tra('The administrator has not yet validated your account. Please wait.'));
				}
			}
		} elseif ($prefs['validateUsers'] == 'y' || !empty($pass)) {
			if ( $mailTemplate == '' ) $mailTemplate = 'user_validation_mail';
			$smarty->assign('mail_pass', $pass);
			$mail_data = $smarty->fetch("mail/$mailTemplate.tpl");
			$mail = new TikiMail();
			$mail->setText($mail_data);
			$mail_data = $smarty->fetch("mail/{$mailTemplate}_subject.tpl");
			$mail->setSubject($mail_data);
			if (!$mail->send(array($email))) {
				$smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
				return false;
			} elseif (empty($again)) {
				$smarty->assign('msg', $smarty->fetch('mail/user_validation_msg.tpl'));
			} else {
				$smarty->assign('msg', tra('You must validate your account first. An email has been sent to you'));
			}
		}
		return true;
	}

	function set_registrationChoice($groups, $flag) {
		$bindvars = array();
		$bindvars[] = $flag;
		if (is_array($groups)) {
			$mid = implode(',',array_fill(0,count($groups),'?'));
			$bindvars = array_merge($bindvars, $groups);
		} else {
			$bindvars[] = $groups;
			$mid = 'like ?';
		}
		$query = "update `users_groups` set `registrationChoice`= ? where `groupName` in ($mid)";
		$result = $this->query($query, $bindvars);
	}

	function get_registrationChoice($group) {
		$query = "select `registrationChoice` from `users_groups` where `groupName` = ?";
		return ($this->getOne($query, array($group)));
	}
	function reset_email_due($user) {
		$query = 'update `users_users` set `email_confirm`=?, `waiting`=? where `login`=?';
		$this->query($query, array(0, 'u', $user));
	}

	function confirm_email($user, $pass) {
		global $prefs, $tikilib;
		$query = 'select `provpass`, `login`, `unsuccessful_logins` from `users_users` where `login`=?';
		$result = $this->query($query, array($user));
		if (!($res = $result->fetchRow())) {
			return false;
		}
		if (md5($res['provpass']) == $pass){
			$this->confirm_user($user);
			$query = 'update `users_users` set `provpass`=?, `email_confirm`=?, `unsuccessful_logins`=?, `registrationDate`=? where `login`=? and `provpass`=?';
			$this->query($query, array('', $tikilib->now, 0, $this->now, $user, $res['provpass']));
			if (!empty($GLOBALS['user'])) {
				global $logslib; include_once('lib/logs/logslib.php');
				$logslib->add_log('login', 'confirm email '.$user);
			}
			return true;
		}
		return false;
	}

	function set_unsuccessful_logins($user, $nb) {
 		$query = 'update `users_users` set `unsuccessful_logins`=? where `login` = ?';
		$this->query($query, array($nb, $user));
	}

	function send_confirm_email($user,$tpl='confirm_user_email') {
		global $smarty, $prefs, $tikilib;
		include_once ('lib/webmail/tikimaillib.php');
		$languageEmail = $this->get_user_preference($_REQUEST["username"], "language", $prefs['site_language']);
		$apass = $this->renew_user_password($user);
		$apass = md5($apass);
		$smarty->assign('mail_apass',$apass);
		$smarty->assign('mail_pass', $_REQUEST['pass']);
		$smarty->assign('mail_ip', $tikilib->get_ip_address()); 
		$smarty->assign('user', $user);
		$mail = new TikiMail();
		$mail_data = $smarty->fetchLang($languageEmail, "mail/$tpl"."_subject.tpl");
		$mail_data = sprintf($mail_data, $_SERVER['SERVER_NAME']);
		$mail->setSubject($mail_data);
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$mail_machine = $tikilib->httpPrefix( true ).str_replace('tiki-login.php', 'tiki-confirm_user_email.php', $foo['path']);
		$smarty->assign('mail_machine', $mail_machine);
		$mail_data = $smarty->fetchLang($languageEmail, "mail/$tpl.tpl");
		$mail->setText($mail_data);
		if (!($email = $this->get_user_email($user)) || !$mail->send(array($email))) {
			$smarty->assign('msg', tra("The user email confirmation can't be sent. Contact the administrator"));
			return false;
		} else {
			$smarty->assign('msg', 'It is time to confirm your email. You will receive an mail with the instruction to follow');
			return true;
		}
	}

	function assign_openid( $username, $openid ) {
		// This won't update the database unless the openid is different
		$this->query("UPDATE `users_users` SET openid_url = ? WHERE login = ? AND ( openid_url <> ? OR openid_url IS NULL )", array( $openid, $username, $openid ));
	}

	function intervalidate($remote,$user,$pass,$get_info = false) {
		global $prefs;
		include_once('XML/RPC.php');
		$hashkey = $this->get_cookie_check() . ".". ($this->now + $prefs['remembertime']);
		$remote['path'] = preg_replace("/^\/?/","/",$remote['path']);
		$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
		$client->setDebug(0);
		$msg = new XML_RPC_Message(
			'intertiki.validate',
			array(
				new XML_RPC_Value($prefs['tiki_key'], 'string'),
				new XML_RPC_Value($user, 'string'),
				new XML_RPC_Value($pass, 'string'),
				new XML_RPC_Value($get_info, 'boolean'),
				new XML_RPC_Value($hashkey, 'string')
			));
		$result = $client->send($msg);
		return $result;
	}

	/* send request + interpret email/login */
	function interGetUserInfo($remote, $user, $email) {
		global $prefs;
		include_once('XML/RPC.php');
		$remote['path'] = preg_replace("/^\/?/","/",$remote['path']);
		$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
		$client->setDebug(0);
		$params = array();
		$params[] = new XML_RPC_Value($prefs['tiki_key'], 'string');
		$params[] = new XML_RPC_Value($user, 'string');
		$params[] = new XML_RPC_Value($email, 'string');
		$msg = new XML_RPC_Message('intertiki.getUserInfo', $params);
		$rpcauth = $client->send($msg);
		if (!$rpcauth || $rpcauth->faultCode()) {
			return false;
		}
		$response_value = $rpcauth->value();
		for (;;) {
			list($key, $value) = $response_value->structeach();
			if ($key == '') {
				break;
			} elseif ($key == 'login') {
				$u['login'] = $value->scalarval();
			} elseif ($key == 'email') {
				$u['email'] = $value->scalarval();
			}
		}
		return $u;
	}
	/* send via XML_RPC user info to the main */
	function interSendUserInfo($remote, $user) {
		global $prefs, $userlib;
		include_once('XML/RPC.php');
		$remote['path'] = preg_replace("/^\/?/","/",$remote['path']);
		$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
		$client->setDebug(0);
		$params = array();
		$params[] = new XML_RPC_Value($prefs['tiki_key'], 'string');
		$params[] = new XML_RPC_Value($user, 'string');
		$user_details = $userlib->get_user_details($user);
		$user_info = $userlib->get_user_info($user);
		$ret['avatarData'] = new XML_RPC_Value($user_info['avatarData'], 'base64');
		$ret['user_details'] = new XML_RPC_Value(serialize($user_details), 'string');
		$params[] = new XML_RPC_Value($ret, 'struct');
		$msg = new XML_RPC_Message('intertiki.setUserInfo', $params);
		$result = $client->send($msg);
		return $result;
	}
	/* interpret the XML_RPC answer about user info */
	function interSetUserInfo($user, $response_value) {
		global $userlib, $tikilib;
		if ($response_value->kindOf() == 'struct') {
			for (;;) {
				list($key, $value) = $response_value->structeach();
				if ($key == '') {
					break;
				} elseif ($key == 'user_details') {
					$user_details = unserialize($value->scalarval());
				} elseif ($key == 'avatarData') {
					$avatarData = $value->scalarval();
				}
			}
		} else {
			$user_details = unserialize($response_value->scalarval());
		}
		$userlib->set_user_fields($user_details['info']);
		$tikilib->set_user_preferences($user, $user_details['preferences']);
		if (isset($avatarData)) {
			global $userprefslib; include_once('lib/userprefs/userprefslib.php');
			$userprefslib->set_user_avatar($user, 'u', '', $user_details['avatarName'], $user_details['avatarSize'], $user_details['avatarFileType'], $avatarData);
		}
	}

	function get_remote_user_by_cookie($hash) {
		global $prefs;
		include_once('XML/RPC.php');
		$prefs['interlist'] = unserialize($prefs['interlist']);
		$remote = $prefs['interlist'][$prefs['feature_intertiki_mymaster']];
		$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
		$client->setDebug(0);
		$msg = new XML_RPC_Message(
			'intertiki.cookiecheck',
			 array(
				 new XML_RPC_Value($prefs['tiki_key'], 'string'),
				 new XML_RPC_Value($hash, 'string')
			 )
		);
		$result = $client->send($msg);
		return $result;
	}
	function update_expired_groups() {
		global $tikilib;
		$query = 'SELECT uu.* FROM `users_usergroups` uu LEFT JOIN `users_groups` ug ON (uu.`groupName`= ug.`groupName`) WHERE ( ug.`expireAfter` > ? AND uu.`created` IS NOT NULL AND uu.`expire` is NULL AND uu.`created` + ug.`expireAfter`*24*60*60 < ?) OR (ug.`expireAfter` = ? AND uu.`expire` < ?)';
		$result = $this->query($query, array(0, $tikilib->now, 0, $tikilib->now ));
		$query = 'DELETE FROM `users_usergroups` WHERE `groupName`=? AND `userId`=?';
		while ($res = $result->fetchrow()) {
			$this->query($query, array($res['groupName'], $res['userId']));
		}
	}

	function extend_membership( $user, $group, $periods = 1 ) {
		global $tikilib;
		$this->update_expired_groups();

		if( ! $this->user_is_in_group( $user, $group ) ) {
			$this->assign_user_to_group( $user, $group );
		}

		$info = $this->get_group_info( $group );
		$userInfo = $this->get_user_info( $user );
		$date = $this->getOne( 'SELECT `expire` FROM `users_usergroups` where `userId` = ? AND `groupName` = ?', array($userInfo['userId'], $group));
		if ($date <= 0)
			$date = $tikilib->now;
		$date += $periods * $info['expireAfter'] * 24 * 3600;

		$this->query( 'UPDATE `users_usergroups` SET `expire` = ? WHERE `userId` = ? AND `groupName` = ?', array(
																												 $date,
			$userInfo['userId'],
			$group,
		) );
	}

	function get_users_created_group($group, $user=null) {
		if (!empty($user)) {
			$query = 'SELECT `users_usergroups`.`created` FROM `users_usergroups` LEFT JOIN `users_users` on (`users_users`.`userId`=`users_usergroups`.`userId`) WHERE `groupName`=? AND `user`=?';
			$bindvars = array($group, $user);
		} else {
			$query = 'SELECT `login`, `users_usergroups`.`created` FROM `users_usergroups` LEFT JOIN `users_users` on (`users_users`.`userId`=`users_usergroups`.`userId`) WHERE `groupName`=?';
			$bindvars = array($group);
		}
		$result = $this->query($query, $bindvars);
		$ret = array();
		while($res = $result->fetchRow()) {
			$ret[$res['login']]= $res['created'];
		}
		return $ret;
	}

	function nb_users_in_group($group=null) {
		if (!empty($group)) {
			$query = 'SELECT count(*) FROM `users_usergroups` WHERE `groupName`=?';
			return $this->getOne($query, array($group));
		} else {
			$query = 'SELECT count(*) FROM `users_users`';
			return $this->getOne($query, array());
		}
	}
	
	function find_best_user($usrs, $group = '', $key = 'login') {
		$finalusers = array();
		foreach ($usrs as $u) {
			$u = trim($u);
			if ($u == 'admin') {
				$finalusers[] = $u;
			} elseif ($key == 'userId' && preg_match('/\(([0-9]+)\)$/', $u, $matches)) {
				$finalusers[] = $this->get_user_login($matches[1]);
			} elseif ($key == 'login' && preg_match('/\((.+)\)$/', $u, $matches)) {
				$finalusers[] = $matches[1];
			} else {
				$possibleusers = $this->get_users_light(0, -1, 'login_asc', '', $group);
				$unames = array_keys($possibleusers, $u);
				if (count($unames) == 1 && $unames[0]) {
					$finalusers[] = $unames[0];
				}
			}
		}
		return $finalusers;	
	}
	
	function clean_user($u, $force_check_realnames = false, $login_fallback = true) {
		global $tikilib, $prefs;
		if ( $prefs['user_show_realnames'] == 'y' || $force_check_realnames) {
			$realname = $tikilib->get_user_preference($u, 'realName', '');
		}
		if (!empty($realname)) {
			$u = trim($realname); // need to trim to prevent mustMatch failure
		} elseif ($prefs['login_is_email_obscure'] == 'y' && $atsign = strpos($u, '@')) {
			$u = substr($u, 0, $atsign);
			if (!$login_fallback) {
				$u = tra('Anonymous');
			}
		}
		return $u;
	}
	
	function categorize_user_tracker_item($user, $group) {
		global $tikilib;
		$userid = $this->get_user_id($user);
		$tracker = $this->get_usertracker($userid);
		if( $tracker && $tracker['usersTrackerId'] ) {
			$trklib = TikiLib::lib('trk');
			$categlib = TikiLib::lib('categ');
			$itemid = $trklib->get_item_id( $tracker['usersTrackerId'], $tracker['usersFieldId'], $user );
			$cat = $categlib->get_object_categories('trackeritem', $itemid);
			$categId = $this->getOne("select `categId` from `tiki_categories` where `name` = ?", array($group));
			$cat[] = $categId;
			$cat = array_unique($cat);
			$trklib->categorized_item($tracker["usersTrackerId"], $itemid, '', $cat);
		}
	}
	
 	function uncategorize_user_tracker_item($user, $group) {
		global $tikilib;
		$userid = $this->get_user_id($user);
		$tracker = $this->get_usertracker($userid);
		if( $tracker && $tracker['usersTrackerId'] ) {
			$trklib = TikiLib::lib('trk');
			$categlib = TikiLib::lib('categ');
			$itemid = $trklib->get_item_id( $tracker['usersTrackerId'], $tracker['usersFieldId'], $user );
			$cat = $categlib->get_object_categories('trackeritem', $itemid);
			$categId = $this->getOne("select `categId` from `tiki_categories` where `name` = ?", array($group));
			$cat = array_diff($cat, array($categId));
			$trklib->categorized_item($tracker["usersTrackerId"], $itemid, '', $cat);
		}
	}

	/**
	 * Remove the link between a Tiki user account
	 * and an OpenID account
	 * 
	 * @param int $userId
	 * @return void
	 */
	function remove_openid_link($userId) {
		$query = "UPDATE `users_users` SET `openid_url` = NULL WHERE `userId` = ?";
		$bindvars = array($userId);
		$this->query($query, $bindvars);
	}

}

/* For the emacs weenies in the crowd.
Local Variables:
   c-basic-offset: 4
End:
*/

