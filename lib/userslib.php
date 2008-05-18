<?php
// CVS: $Id: userslib.php,v 1.247.2.30 2008-03-22 12:21:02 sylvieg Exp $
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
define ("USER_AMBIGOUS", -7);
define('USER_NOT_VALIDATED', -8);

//added for Auth v1.3 support
define ("AUTH_LOGIN_OK", 0);

class UsersLib extends TikiLib {
# var $db;  // The PEAR db object used to access the database

    // change this to an email address to receive debug emails from the LDAP code
    var $debug = false;

    var $usergroups_cache;
    var $groupperm_cache;
    var $groupinclude_cache;
    var $userobjectperm_cache; // used to cache queries in object_has_one_permission()

    function UsersLib($db) {
	$this->TikiLib($db);

	// Initialize caches
	$this->usergroups_cache = array();
	$this->groupperm_cache = array(array());
	$this->groupinclude_cache = array();
    }

    function set_admin_pass($pass) {
	global $prefs;

	$query = "select `email` from `users_users` where `login` = ?";
	$email = $this->getOne($query, array('admin'));
	$hash = $this->hash_pass($pass);

	if ($prefs['feature_clear_passwords'] == 'n')
	    $pass = '';

	$query = "update `users_users` set `password` = ?, hash = ?
	    where `login` = ?";
	$result = $this->query($query, array($pass, $hash, 'admin'));
	return true;
    }

    function assign_object_permission($groupName, $objectId, $objectType, $permName) {
	$objectId = md5($objectType . strtolower($objectId));

	$query = "delete from `users_objectpermissions`
	    where `groupName` = ? and
	    `permName` = ? and
	    `objectId` = ?";
	$result = $this->query($query, array($groupName, $permName,
		    $objectId), -1, -1, false);

	$query = "insert into `users_objectpermissions`(`groupName`,
	`objectId`, `objectType`, `permName`)
	    values(?, ?, ?, ?)";
	$result = $this->query($query, array($groupName, $objectId,
		    $objectType, $permName));
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
    
    // assign permissions for an individual object according to the global permissions for that object type
    function inherit_global_permissions($objectId, $objectType) {
    	global $cachelib;
    	
		// check for annoying cases where some tables in the DB use singular and others use plural
    	if ($objectType == 'category') {
    		$objectType2 = 'categories';
    	} else {
    		$objectType2 = $objectType;
    	}
    	
		$groups = $this->get_groups();
		if (!$cachelib->isCached($objectType2 . "_permission_names")) {
			$perms = $this->get_permissions(0, -1, 'permName_desc', $objectType2);
			$cachelib->cacheItem($objectType2 . "_permission_names",serialize($perms));
		} else {
			$perms = unserialize($cachelib->getCached($objectType2 . "_permission_names"));
		}
		foreach ($groups['data'] as $group) {
			foreach ($perms['data'] as $perm) {
				if (in_array($perm['permName'], $group['perms'])) {
					$this->assign_object_permission($group['groupName'], $objectId, $objectType, $perm['permName']);
				}
			}
		}
    }

    function get_object_permissions($objectId, $objectType) {
	$objectId = md5($objectType . strtolower($objectId));

	$query = "select `groupName`, `permName`
	    from `users_objectpermissions`
	    where `objectId` = ? and
	    `objectType` = ?";
	$bindvars = array($objectId, $objectType);
	$result = $this->query($query, $bindvars);
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $ret[] = $res;
	}

	return $ret;
    }
	function get_object_permissions_for_user ($objectId, $objectType, $user) {
		$objectId = md5($objectType . strtolower($objectId));
		$bindvars = array($objectId, $objectType);
		$groups = $this->get_user_groups($user);
		$bindvars = array_merge($bindvars, $groups);
		$query = 'select `permName` from `users_objectpermissions`  where `objectId` = ? and `objectType` = ?  and `groupName` in ('.implode(',', array_fill(0, count($groups),'?')).')';
		$result = $this->query($query, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['permName'];
		}
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

    function group_exists($group) {
	static $rv = array();

	if (!isset($rv[$group])) {
	    $query = "select count(`groupName`)  from `users_groups` where `groupName` = ?";

	    $result = $this->getOne($query, array($group));
	    $rv[$group] = $result;
	}

	return $rv[$group];
    }

    function user_logout($user) {
			global $prefs;
			$query = 'delete from `tiki_user_preferences` where `prefName`=? and `user`=?';
			$user = $this->query($query, array('cookie',(string)$user));
			if ($prefs['feature_intertiki'] == 'y' and $prefs['feature_intertiki_sharedcookie'] == 'y' and !empty($prefs['feature_intertiki_mymaster'])) {
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
							 ));
				$client->send($msg);
			}
    }
    
    function genPass() {
	// AWC: enable mixed case and digits, don't return too short password
	global $prefs;                                          //AWC

	$vocales = "AaEeIiOoUu13580";                                     //AWC
	$consonantes = "BbCcDdFfGgHhJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz24679"; //AWC
	$r = '';
	$passlen = ($prefs['min_pass_length'] > 5) ? $prefs['min_pass_length'] : 5;         //AWC

	for ($i = 0; $i < $passlen; $i++) {                               //AWC
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
	return $this->db->getOne(
		"select count(*) from `users_users` where " . $this->convert_binary(). " `login` = ? and `hash`=?",
		array($user, $hash)
		);
    }

	// For each auth method, validate user in auth, if valid, verify tiki user exists and create if necessary (as configured)
	// Once complete, update_lastlogin and return result, username and login message.
	function validate_user($user, $pass, $challenge, $response, $validate_phase=false) {
	global $tikilib, $prefs, $user_ldap_attributes;

	if ($user != 'admin' && $prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
	    // slave intertiki sites should never check passwords locally, just for admin
	    return false;
	}

	if (strlen($pass) < $prefs['min_pass_length']) {
		return false;
	}
	// these will help us keep tabs of what is going on
	$userTiki = false;
	$userTikiPresent = false;
	$userAuth = false;
	$userAuthPresent = false;

	// read basic pam options
	$auth_pam = ($prefs['auth_method'] == 'pam');
	$pam_create_tiki = ($prefs['pam_create_user_tiki'] == 'y');
	$pam_skip_admin = ($prefs['pam_skip_admin'] == 'y');

	// read basic PEAR:Auth options
	$auth_pear = ($prefs['auth_method'] == 'auth');
	$create_tiki = ($prefs['auth_create_user_tiki'] == 'y');
	$create_auth = ($prefs['auth_create_user_auth'] == 'y');
	$skip_admin = ($prefs['auth_skip_admin'] == 'y');
	
	// read basic cas options
	global $phpcas_enabled;
	if ($phpcas_enabled == 'y') {
		$auth_cas = ($prefs['auth_method'] == 'cas');
		$cas_create_tiki = ($prefs['cas_create_user_tiki'] == 'y');
		$cas_skip_admin = ($prefs['cas_skip_admin'] == 'y');
	} else {
		$auth_cas = $cas_create_tiki = $cas_skip_admin = false;
	}

	// see if we are to use Shibboleth
	$auth_shib = ($prefs['auth_method'] == 'shib');
	$shib_create_tiki = ($prefs['shib_create_user_tiki'] == 'y');
	$shib_skip_admin = ($prefs['shib_skip_admin'] == 'y');

	// first attempt a login via the standard Tiki system
	// 
	if (!($auth_shib || $auth_cas) || $user == 'admin') {
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
	if ((!$auth_pear && !$auth_pam && !$auth_cas && !$auth_shib) || ((($auth_pear && $skip_admin) || ($auth_shib && $shib_skip_admin) || ($auth_pam && $pam_skip_admin) || ($auth_cas && $cas_skip_admin)) && $user == "admin") || ($auth_pear && $prefs['auth_create_user_tiki'] == 'y' && $userTiki)) {
	    // if the user verified ok, log them in
	    if ($userTiki)  //user validated in tiki, update lastlogin and be done
		return array($this->update_lastlogin($user), $user, $result);
	    // if the user password was incorrect but the account was there, give an error
	    elseif ($userTikiPresent)  //user ixists in tiki but bad password
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
			return array($this->update_lastlogin($user), $user, $result);
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
					return array($this->update_lastlogin($user), $user, $result);
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
			return array($this->update_lastlogin($user), $user, $result);
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
	    if ($userCAS && $userTiki) {
			return array($this->update_lastlogin($user), $user, $result);
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
					return array($this->update_lastlogin($user), $user, $result);
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
			return array($this->update_lastlogin($user), $user, $result);
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
		$shibaffiliarray = split(";",strtoupper($shibaffiliation));
		$validaffiliarray = split(",",strtoupper($prefs['shib_affiliation']));
		$validafil=false;
		foreach($shibaffiliarray as $affil){
		   if(in_array($affil, $validaffiliarray)){
			   $validafil=true;    
		   }
	   }

    	// start off easy
	    // if the user verified in Tiki and by Shibboleth, log in
	    if ($userTikiPresent AND $validafil) {
			return array($this->update_lastlogin($user), $user, USER_VALID);
	    }
	    else {
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

					$url = 'tiki-error.php?error=' . $errmsg;
					header("location: $url");
					die;
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
							return array($this->update_lastlogin($user), $user, $result);
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
							$vaffils = $vaffils  . $vaffil . ", ";
						}
						$vaffils = rtrim($vaffils,", ");
						$url = "tiki-error.php?error=<H1 align=center>User login error</H1><BR/><BR/>You must have one of the following affiliations to get into this wiki.<BR/><BR/><B>" . $vaffils . "</B><BR><BR/><BR/>For further information on this error goto the <a href=./tiki-index.php?page=ShibReg>Shibreg</a> Page";
						header("location: $url");
						die;
					}
				}			
			}
			else{
				header("location: tiki-error.php?error=The user [ " . $user . " ] is not registered with this wiki.");
				die;
			}
			
	    }

	}

	// next see if we need to check LDAP
	else {
	    // check the user account
	    $result = $this->validate_user_auth($user, $pass);

	    switch ($result) {
		case USER_VALID:
		    $userAuth = true;

		    $userAuthPresent = true;
		    break;

		case PASSWORD_INCORRECT:
		    $userAuthPresent = true;

		    break;
	    }

	    // start off easy
	    // if the user verified in Tiki and Auth, log in
	    if ($userAuth && $userTiki) {
		return array($this->update_lastlogin($user), $user, $result);
	    }
	    // if the user wasn't found in either system, just fail
	    elseif (!$userTikiPresent && !$userAuthPresent) {
		return array(false, $user, $result);
	    }
	    // if the user was logged into Tiki but not found in Auth
	    elseif ($userTiki && !$userAuthPresent) {
		// see if we can create a new account
		if ($create_auth) {
		    // need to make this better! *********************************************************
		    $result = $this->create_user_auth($user, $pass);

		    // if it worked ok, just log in
		    if ($result == USER_VALID)
			// before we log in, update the login counter
			return array($this->update_lastlogin($user), $user, $result);
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
	    elseif ($userAuth && !$userTikiPresent) {
		// see if we can create a new account
		if ($create_tiki) {
		    // need to make this better! *********************************************************
		    $result = $this->add_user($user, $pass, '');

		    // if it worked ok, just log in
		    if ($result == USER_VALID) {
			// before we log in, update the login counter
			return array($this->update_lastlogin($user), $user, $result);
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
	    elseif ($userAuth && $userTikiPresent)
		return array($this->update_lastlogin($user), $user, $result);
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
	// error_log("TIKI ERROR PAM:  $error User: $user Pass: $pass");
		return PASSWORD_INCORRECT;
	}
    }
    
	// validate the user through CAS
	function validate_user_cas(&$user) {
		global $tikilib, $phpcas_enabled, $prefs;
		if ($phpcas_enabled != 'y') {
			return SERVER_ERROR;
		}
		// just make sure we're supposed to be here
		if ($prefs['auth_method'] != 'cas') {
		    return false;
		}

		// import phpCAS lib
		require_once('phpcas/source/CAS/CAS.php');

		phpCAS::setDebug();

		// initialize phpCAS
		phpCAS::client($prefs['cas_version'], ''.$prefs['cas_hostname'], (int) $prefs['cas_port'], ''.$prefs['cas_path']);

		// check CAS authentication
		phpCAS::forceAuthentication();

		// at this step, the user has been authenticated by the CAS server
		// and the user's login name can be read with phpCAS::getUser().
		
		$user = phpCAS::getUser();
		
		if (isset($user)) {
			return USER_VALID;
		} else {
			return PASSWORD_INCORRECT;
		}
    }

    // validate the user in the PEAR::Auth system
    function validate_user_auth($user, $pass) {
	global $tikilib, $user_ldap_attributes, $prefs;

	include_once ("Auth/Auth.php");

	// just make sure we're supposed to be here
	if ($prefs['auth_method'] != 'auth')
	    return false;

	// get all of the LDAP options from the database
	$options['url'] = $prefs['auth_ldap_url'];
	$options['host'] = $prefs['auth_pear_host'];
	$options['port'] = $prefs['auth_pear_port'];
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
	$options['version'] = $prefs['auth_ldap_version'];

	//added to allow for ldap systems that do not allow anonymous bind
	$options['binddn'] = $prefs['auth_ldap_adminuser'];
	$options['bindpw'] = $prefs['auth_ldap_adminpass'];

	// attributes to fetch
	$options['attributes'] = array();
	if ( $nameattr = $prefs['auth_ldap_nameattr'] ) $options['attributes'][] = $nameattr;

	// set the Auth options
	//$a = new Auth('LDAP', $options, '', false, $user, $pass);
	
	//corrected for the Auth v.13 upgrade
	$a = new Auth('LDAP', $options, '', false);

	//added to support Auth v1.3
	$a->username = $user;
	$a->password = $pass;
	$a->status = AUTH_LOGIN_OK;

	// check if the login correct
	$a->login();
	switch ($a->getStatus()) {
		case AUTH_LOGIN_OK:
			// Retrieve LDAP information to update user data a bit later (when he will be completely validated or auto-created)
			if ( $nameattr != '' ) $user_ldap_attributes['auth_ldap_nameattr'] = $a->getAuthData($nameattr);
			return USER_VALID;

		case AUTH_USER_NOT_FOUND:
			return USER_NOT_FOUND;

		case AUTH_WRONG_LOGIN:
			return PASSWORD_INCORRECT;

		default:
			return SERVER_ERROR;
	}
    }

    // validate the user in the Tiki database
    function validate_user_tiki($user, $pass, $challenge, $response, $validate_phase=false) {
	global $prefs;

	// first verify that the user exists
	$query = "select * from `users_users` where " . $this->convert_binary(). " `login` = ?";
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

	// Temporary escape of the process until the email confirmation gets repaired
	// In the case the account was created with OpenID, no need to confirm the account
	// beyond this point.
	if( !empty( $res['openid_url'] ) && empty( $res['hash'] ) )
		return array(USER_VALID, $user);

	// next verify the password with every hashes methods
	if ($prefs['feature_challenge'] == 'n' || empty($response)) {
	    if ($res['hash'] == md5($user.$pass.trim($res['email']))) // very old method md5(user.pass.email), for compatibility
 		return array(USER_VALID, $user);

	    if ($res['hash'] == md5($user.$pass)) // old method md5(user.pass), for compatibility
		return array(USER_VALID, $user);
 
	    if ($res['hash'] == md5($pass)) // normal method md5(pass)
		return array(USER_VALID, $user);
 
	    if ($this->hash_pass($pass, $res['hash']) == $res['hash']) // new method (crypt-md5) and tikihash method (md5(pass))
		return array(USER_VALID, $user);
		
		if ($res['valid'] > '' && $pass == $res['valid']) // used for validation of user account before activation
		return array(USER_VALID, $user);

		if (!empty($res['valid']))
			return array(ACCOUNT_DISABLED, $user);
	    return array(PASSWORD_INCORRECT, $user);
	} else {
	    // Use challenge-reponse method
	    // Compare pass against md5(user,challenge,hash)
	    $hash = $this->getOne("select `hash`  from `users_users` where " . $this->convert_binary(). " `login`=?",
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

    // update the lastlogin status on this user
    function update_lastlogin($user) {
	// Check
	$current = $this->getOne("select `currentLogin` from `users_users` where `login`= ?", array($user));

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
		    ));

	return true;
    }

    // create a new user in the Auth directory
    function create_user_auth($user, $pass) {
	global $tikilib, $prefs;

	$options = array();
	$options['url'] = $prefs['auth_ldap_url'];
	$options['host'] = $prefs['auth_pear_host'];
	$options['port'] = $prefs['auth_pear_port'];
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
	$a = new Auth("LDAP", $options);

	// check if the login correct
	if ($a->addUser($user, $pass, $userattr) === true)
	    $status = USER_VALID;

	// otherwise use the error status given back
	else
	    $status = $a->getStatus();


	return $status;
    }

    function get_users_names($offset = 0, $maxRecords = -1, $sort_mode = 'login_asc', $find = '') {

	// Return an array of users indicating name, email, last changed pages, versions, lastLogin
	if ($find) {
	    $findesc = '%' . $find . '%';
	    $mid = " where `login` like ?";
	    $bindvars=array($findesc);
	} else {
	    $mid = '';
	    $bindvars=array();
	}

	$query = "select `login` from `users_users` $mid order by ".$this->convert_sortmode($sort_mode);
	$result = $this->query($query,$bindvars,$maxRecords,$offset);
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $ret[] = $res["login"];
	}

	return ($ret);
    }

function get_users($offset = 0, $maxRecords = -1, $sort_mode = 'login_asc', $find = '', $initial = '', $inclusion=false, $group='', $email='') {
	
	$mid = '';
	$bindvars = array();
	$mmid = '';
	$mbindvars = array();
	// Return an array of users indicating name, email, last changed pages, versions, lastLogin 
	
	//TODO : recurse included groups 
	if($group) {
		if (!is_array($group)) {
			$group = array($group);
		}
		$mid = ', `users_usergroups` uug where uu.`userId`=uug.`userId` and uug.`groupName` in ('.implode(',',array_fill(0, count($group),'?')).')';
		$mmid = $mid;
		$bindvars = $group;
		$mbindvars = $bindvars;
	}
	if($email) {
		$mid.= $mid == '' ? ' where' : ' and';
		$mid.= ' uu.`email` like ?';
		$mmid = $mid;
	    	$bindvars[] = '%'.$email.'%';
		$mbindvars[] = '%'.$email.'%';
	}
	
	if ($find) {
	    $mid.= $mid == '' ? ' where' : ' and';
	    $mid.= " uu.`login` like ?";
			$mmid = $mid;
	    $bindvars[] = '%'.$find.'%';
			$mbindvars[] = '%'.$find.'%';
	}

	if ($initial) {
		$mid = " where `login` like ?";
		$mmid = $mid;
		$bindvars = array($initial.'%');
		$mbindvars = $bindvars;
	}

	$query = "select uu.* from `users_users` uu $mid order by ".$this->convert_sortmode($sort_mode);

	$query_cant = "select count(*) from `users_users` uu $mmid";
	$result = $this->query($query, $bindvars, $maxRecords, $offset);
	$cant = $this->getOne($query_cant, $mbindvars);
	$ret = array();

	while ($res = $result->fetchRow()) {
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
		
	    $ret[] = $res;
	}

	$retval = array();
	$retval["data"] = $ret;
	$retval["cant"] = $cant;
	return $retval;
}

    function group_inclusion($group, $include) {
	$query = "insert into `tiki_group_inclusion`(`groupName`,`includeGroup`)
		values(?,?)";
	$result = $this->query($query, array($group, $include));
    }

function get_included_groups($group, $recur=true) {
	$engroup = urlencode($group);
	if (!$recur || !isset($this->groupinclude_cache[$engroup])) {
		$query = "select `includeGroup`  from `tiki_group_inclusion` where `groupName`=?";
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
		if (!$recur) {
			$this->groupinclude_cache[$engroup] = $back;
		}
		return $back; 
	} else {
		return $this->groupinclude_cache[$engroup];
	}
}
	function get_including_groups($group) {
		$query = 'select `groupName` from `tiki_group_inclusion` where `includeGroup`=?';
		$result = $this->query($query, array($group));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['groupName'];
			$ret = array_merge($ret, $this->get_including_groups($res['groupName']));
		}
		return $ret;
	}

    function remove_user_from_group($user, $group) {
	global $cachelib; require_once("lib/cache/cachelib.php");
	global $tikilib;
	$cachelib->invalidate('user_details_'.$user);
	$tikilib->invalidate_usergroups_cache($user);

	$userid = $this->get_user_id($user);

	$query = "delete from `users_usergroups` where `userId` = ? and
		`groupName` = ?";
	$result = $this->query($query, array($userid, $group));
    }

    function remove_user_from_all_groups($user) {
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

	$query = "select `groupName` , `groupDesc`, `registrationChoice`, `userChoice` from `users_groups` $mid order by ".$this->convert_sortmode($sort_mode);
	$query_cant = "select count(*) from `users_groups` $mid";
	$result = $this->query($query, $bindvars, $maxRecords, $offset);
	$cant = $this->getOne($query_cant, $bindvars);
	$ret = array();

	while ($res = $result->fetchRow()) {
	    if ($details == "y") {
	    	$perms = $this->get_group_permissions($res['groupName']);
	    	$res['perms'] = $perms;
		$res['permcant'] = count($perms);
	    	$groups = $this->get_included_groups($res['groupName']);
	    	$res['included'] = $groups;
	    }
	    $ret[] = $res;
	}
	
	$retval = array();
	$retval["data"] = $ret;
	$retval["cant"] = $cant;
	return $retval;
    }
	
	function list_all_users() {
		global $cachelib;
		if (!$cachelib->isCached("userslist")) {
			$users = array();
			$result = $this->query("select `login`,`userId` from `users_users` order by `login`", array());
			while ($res = $result->fetchRow()) {
				$users["{$res['userId']}"] = $res['login'];
			}
			$cachelib->cacheItem("userslist",serialize($users));
			return $users;
		} else {
			return unserialize($cachelib->getCached("userslist"));
		}
	}

	function list_all_groups() {
		global $cachelib;
		if (!$cachelib->isCached("grouplist")) {
			$groups = array();
			$result = $this->query("select `groupName` from `users_groups` order by `groupName`", array());
			while ($res = $result->fetchRow()) {
				$groups[] = $res['groupName'];
			}
			$cachelib->cacheItem("grouplist",serialize($groups));
			return $groups;
		} else {
			return unserialize($cachelib->getCached("grouplist"));
		}
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

	$userId = $this->getOne("select `userId`  from `users_users` where `login` = ?", array($user));

	$query = "delete from `users_users` where ". $this->convert_binary()." `login` = ?";
	$result = $this->query($query, array( $user ) );
	$query = "delete from `users_usergroups` where `userId`=?";
	$result = $this->query($query, array( $userId ) );
	$query = "delete from `tiki_user_watches` where ". $this->convert_binary()." `user`=?";
	$result = $this->query($query, array($user));
	$query = "delete from `tiki_user_preferences` where ". $this->convert_binary()." `user`=?";
	$result = $this->query($query, array($user));
	$query = "delete from `tiki_newsletter_subscriptions` where ". $this->convert_binary()." `email`=? and `isUser`=?";
	$result = $this->query($query, array($user, 'y'));

	$cachelib->invalidate('userslist');
	return true;
    }

	function change_login($from,$to) {
		global $cachelib;
		if ( $from == 'admin' ) return false;

		$userId = $this->getOne("select `userId`  from `users_users` where `login` = ?", array($from));
		if ($userId) {
			$this->query("update `users_users` set `login`=? where `userId` = ?", array($to,(int)$userId));
			$this->query("update `tiki_wiki_attachments` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_webmail_messages` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_webmail_contacts` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_webmail_contacts_fields` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_users` set `user`=? where `user`=?", array($to,$from));
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
			$this->query("update `tiki_user_modules` set `name`=? where `name`=?", array($to,$from));
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
			$this->query("update `tiki_newsreader_servers` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_newsreader_marks` set `user`=? where `user`=?", array($to,$from));
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
			$this->query("update `tiki_drawings` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_copyrights` set `userName`=? where `userName`=?", array($to,$from));
			$this->query("update `tiki_comments` set `userName`=? where `userName`=?", array($to,$from));
			$this->query("update `tiki_chat_users` set `nickname`=? where `nickname`=?", array($to,$from));
			$this->query("update `tiki_chat_messages` set `poster`=? where `poster`=?", array($to,$from));
			$this->query("update `tiki_chat_channels` set `moderator`=? where `moderator`=?", array($to,$from));
			$this->query("update `tiki_charts_votes` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_calendars` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_calendar_roles` set `username`=? where `username`=?", array($to,$from));
			$this->query("update `tiki_calendar_items` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_blogs` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_banning` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_banners` set `client`=? where `client`=?", array($to,$from));
			$this->query("update `tiki_articles` set `author`=? where `author`=?", array($to,$from));
			$this->query("update `tiki_actionlog` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `messu_messages` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `messu_messages` set `user_from`=? where `user_from`=?", array($to,$from));
			$this->query("update `galaxia_workitems` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `galaxia_user_roles` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `galaxia_instances` set `owner`=? where `owner`=?", array($to,$from));
			$this->query("update `galaxia_instances` set `nextUser`=? where `nextUser`=?", array($to,$from));
			$this->query("update `galaxia_instance_comments` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `galaxia_instance_activities` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_newsletter_subscriptions` set `email`=? where `email`=? and `isUser`=?", array($to,$from, 'y'));
			$this->query("update `tiki_friends` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_friends` set `friend`=? where `friend`=?", array($to,$from));
			$this->query("update `tiki_friendship_requests` set `userFrom`=? where `userFrom`=?", array($to,$from));
			$this->query("update `tiki_friendship_requests` set `userTo`=? where `userTo`=?", array($to,$from));
			$this->query("update `tiki_freetagged_objects` set `user`=? where `user`=?", array($to,$from));

			$cachelib->invalidate('userslist');
			return true;
		} else {
			return false;
		}
	}

    function remove_group($group) {
	global $cachelib;
	if ( $group == 'Anonymous' || $group == 'Registered' ) return false;

	$query = "delete from `users_groups` where `groupName` = ?";
	$result = $this->query($query, array($group));
	$query = "delete from `tiki_group_inclusion` where `groupName` = ? or `includeGroup` = ?";
	$result = $this->query($query, array($group, $group));
	$query = "delete from `users_usergroups` where `groupName` = ?";
	$result = $this->query($query, array($group));
	$query = "delete from `users_grouppermissions` where `groupName` = ?";
	$result = $this->query($query, array($group));
	$query = "delete from `users_objectpermissions` where `groupName` = ?";
	$result = $this->query($query, array($group));
	$query = "delete from `tiki_newsletter_groups` where `groupName` = ?";
	$result = $this->query($query, array($group));
	$query = "delete from `tiki_newsreader_marks` where `groupName` = ?";
	$result = $this->query($query, array($group));
	
	$cachelib->invalidate('grouplist');
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
				return $home;
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
		return $home;
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

	/* Returns a theme/style for this group. It
	* should honour any established precedence on theme policy
	* TODO Enforce this style to propagate to template dir (get $resg templates, not default site style)
	*/
	function get_user_group_theme($user) {
		global $tikilib;

		$result = $this->get_user_default_group($user);
		if ( isset($result) && ($result!="") ) {
			$query = "select `groupTheme` from `users_groups` where `groupName` = ?";
			$resg = $this->getOne($query, array($result));
			if ( isset($resg) && ($resg != "") ) {
				return $resg;
			}
		}
		return $tikilib->get_preference("style", "default.css");
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

    function get_group_users($group) {
	$query = "select `login`  from `users_users` uu, `users_usergroups` ug where uu.`userId`=ug.`userId` and `groupName`=?";
	$result = $this->query($query,array($group));
	$ret = array();
	while ($res = $result->fetchRow()) {
	    $ret[] = $res["login"];
	}
	return $ret;
    }

    function get_user_info($user, $inclusion = false, $field = 'login') {
    	global $prefs;
	if ( $field == 'userId' ) $user = (int)$user;
	elseif ( $field != 'login' ) return false;

	$result = $this->query("select * from `users_users` where `$field`=?", array($user));
	$res = $result->fetchRow();

	$res['groups'] = ( $inclusion ) ? $this->get_user_groups_inclusion($user) : $this->get_user_groups($user);
	$res['age'] = ( ! isset($res['registrationDate']) ) ? 0 : $this->now - $res['registrationDate'];
	if ( $prefs['login_is_email'] == 'y' && isset($res['login']) && $res['login'] != 'admin' ) $res['email'] = $res['login'];

	return $res;
    }

    function get_userid_info($user, $inclusion = false) { return $this->get_user_info($user, $inclusion, 'userId'); }
    
    // this is not being used anywhere until now in remote.php
    // refactoring to use new cachelib instead of global var in memory - batawata 2006-02-07
    function get_user_details($login, $item = false) {
	global $cachelib;
	require_once("lib/cache/cachelib.php");

	$cacheKey = 'user_details_'.$login;

	$user_details = array();

	if ($cachelib->isCached($cacheKey)) {
	    return unserialize($cachelib->getCached($cacheKey));
	} else {
	    $query  = 'SELECT `userId` , `login`, `email` , `lastLogin` , `currentLogin` , `registrationDate` , `created` ,  `avatarName` , `avatarSize` , `avatarFileType` , `avatarLibName` , `avatarType` FROM `users_users` WHERE `login` = ?';
	    
	    $result = $this->query($query, array($login));
	    
	    $user_details['info'] = $result->fetchRow();
	    
	    $query  = 'SELECT `prefName` , `value` FROM `tiki_user_preferences` WHERE `user` = ?';
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

	    return $user_details;
	}
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

    function change_permission_level($perm, $level) {
    global $cachelib;

    $cachelib->invalidate("allperms");

	$query = "update `users_permissions` set `level` = ?
		where `permName` = ?";
	$this->query($query, array($level, $perm));
    }

    function assign_level_permissions($group, $level) {
    global $cachelib;
    $cachelib->invalidate("allperms");

	$query = "select `permName` from `users_permissions` where `level` = ?";
	$result = $this->query($query, array($level));
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $this->assign_permission_to_group($res['permName'], $group);
	}
    }

    function remove_level_permissions($group, $level) {
    global $cachelib;

    $cachelib->invalidate("allperms");

	$query = "select `permName` from `users_permissions` where `level` = ?";

	$result = $this->query($query, array($level));
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $this->remove_permission_from_group($res['permName'], $group);
	}
    }

    function create_dummy_level($level) {
    global $cachelib;

    $cachelib->invalidate("allperms");

	$query = "delete from `users_permissions` where `permName` = ?";
	$result = $this->query($query, array(''));
	$query = "insert into `users_permissions`(`permName`, `permDesc`,
		`type`, `level`) values('','','',?)";
	$this->query($query, array($level));
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
			$query = 'select `groupName`, `usersTrackerId`, `usersFieldId` from `users_groups` where `groupName` in ('.implode(',',array_fill(0,count($groups),'?')).') and `groupName` != ? and `usersTrackerId` > 0';
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
		$utr = $this->get_userid_info($uid);
		$utr["usersTrackerId"] = '';
		foreach ($utr['groups']  as $gr) {
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

  function get_permissions($offset = 0, $maxRecords = -1, $sort_mode = 'permName_asc', $find = '', $type = '', $group = '') {
	$values = array();
	$sort_mode = $this->convert_sortmode($sort_mode);
	$mid = '';
	if ($type) {
	    $mid = ' where `type`= ? ';
	    $values[] = $type;
	}

	if ($find) {
	    if ($mid) {
		$mid .= " and `permName` like ?";
		$values[] = '%'.$find.'%';
	    } else {
		$mid .= " where `permName` like ?";
		$values[] = '%'.$find.'%';
	    }
	} else {
		if ($mid) {
		$mid .= " and `permName` > ''";		
	    } else {
		$mid .= " where `permName` > ''";		
	    }
	}
	$query = "select * from `users_permissions` $mid order by $sort_mode ";

#	$query_cant = "select count(*) from `users_permissions` $mid";
	$result = $this->query($query, $values, $maxRecords, $offset);
#	$cant = $this->getOne($query_cant, $values);
	$cant = 0;
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $cant++;
	    if ($group && $this->group_has_permission($group, $res['permName'])) {
		$res['hasPerm'] = 'y';
	    } else {
		$res['hasPerm'] = 'n';
	    }

	    $ret[] = $res;
	}

	return array(
		'data' => $ret,
		'cant' => $cant,
		);
    }

    function get_group_permissions($group) {
	$query = "select `permName` from `users_grouppermissions` where `groupName`=?";

	$result = $this->query($query, array($group));
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $ret[] = $res["permName"];
	}

	return $ret;
    }

    function get_user_detailled_permissions($user) {
	$groups = $this->get_user_groups($user);
	$ret = array();
	$query = 'select distinct up.* from `users_permissions` as up, `users_grouppermissions` as ug where ug.`groupName` in ('.implode(',',array_fill(0,count($groups),'?')).') and up.`permName`=ug.`permName`';
	$result = $this->query($query, $groups);
	while ($res = $result->fetchRow()) {
		$ret[] = $res;
	}
	return $ret;
    }

    function assign_permission_to_group($perm, $group) {
    global $cachelib;

    $cachelib->invalidate("allperms");

	$query = "delete from `users_grouppermissions` where `groupName` = ?
		and `permName` = ?";
	$result = $this->query($query, array($group, $perm));
	$query = "insert into `users_grouppermissions`(`groupName`, `permName`)
		values(?, ?)";
	$result = $this->query($query, array($group, $perm));
	return true;
    }

    function get_user_permissions($user) {
	//debug
	//echo "<pre>userslib.php: get_user_permissions\n</pre>";
	// admin has all the permissions
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
	// admin has all the permissions
	if ($user == 'admin')
	    return true;

	// Get user_groups ?
	$groups = $this->get_user_groups($user);

	foreach ($groups as $group) {
	    if ($this->group_has_permission($group, $perm))
		return true;
	}

	return false;
    }

    function group_has_permission($group, $perm) {
		$engroup = urlencode($group);
	if (!isset($perm, $this->groupperm_cache[$engroup][$perm])) {
	    $query = "select count(*) from `users_grouppermissions` where `groupName`=? and `permName`=?";
	    $result = $this->getOne($query, array( $group, $perm));
	    $this->groupperm_cache[$engroup][$perm] = $result;
	    return $result;
	} else {
	    return $this->groupperm_cache[$engroup][$perm];
	}
    }

    function remove_permission_from_group($perm, $group) {
    global $cachelib;

    $cachelib->invalidate("allperms");

	$query = "delete from `users_grouppermissions` where `permName` = ?
		and `groupName` = ?";
	$result = $this->query($query, array($perm, $group));
	return true;
    }

    function get_group_info($group) {
	$query = "select * from `users_groups` where `groupName`=?";

	$result = $this->query($query, array($group));
	$res = $result->fetchRow();
	$perms = $this->get_group_permissions($group);
	$res["perms"] = $perms;
	return $res;
    }

    function assign_user_to_group($user, $group) {
	global $cachelib; require_once("lib/cache/cachelib.php");
	global $tikilib;
	$cachelib->invalidate('user_details_'.$user);
	$tikilib->invalidate_usergroups_cache($user);

	$group_ret = false;
	$userid = $this->get_user_id($user);

	if ( $userid > 0 ){
	    $query = "insert into `users_usergroups`(`userId`,`groupName`) values(?,?)";
	    $result = $this->query($query, array(
		    $userid,
		    $group
		    ), -1, -1, false);
	    $group_ret = true;
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
	    
	case 'tikihash':
	default:
	    return md5($pass);
	}
    }

    function confirm_user($user) {
	global $prefs,$cachelib;

	$query = "select `provpass`, `login` from `users_users` where `login`=?";
	$result = $this->query($query, array($user));
	$res = $result->fetchRow();
	$hash = $this->hash_pass($res['provpass']);
	$provpass = $res["provpass"];

	if ($prefs['feature_clear_passwords'] == 'n') {
	    $provpass = '';
	}

	$query = "update `users_users` set `password`=? ,`hash`=? ,`provpass`=?, valid=?, `email_confirm`=?, `waiting`=? where `login`=?";
	$result = $this->query($query, array(
		    $provpass,
		    $hash,
		    '',
			NULL,
			$this->now,
			NULL,
		    $user
		    ));
	$cachelib->invalidate('userslist');
    }

	function change_user_waiting($user, $who) {
		$query = 'update `users_users` set `waiting`=?, `currentLogin`=?, `lastLogin`=? where `login`=?';
		$this->query($query, array($who, NULL, NULL, $user));
	}

    function add_user($user, $pass, $email, $provpass = '',$pass_first_login=false, $valid=NULL, $openid_url=NULL) {
	global $tikilib, $cachelib, $patterns, $prefs;
	
	if ($this->user_exists($user) || empty($user) || !preg_match($patterns['login'],$user) || strtolower($user) == 'anonymous' || strtolower($user) == 'registered')
	    return false;

	// Generate a unique hash; this is also done below in set_user_fields()
	$lastLogin = null;
	if (empty($openid_url))
	{
		$hash = $this->hash_pass($pass);
	}
	else
	{
		$hash = '';
		if (!isset($prefs['validateRegistration']) || $prefs['validateRegistration'] != 'y')  $lastLogin = time();
	}
		
	if ($valid == 'n') {
		$valid = $pass;
	}

	if ( $prefs['feature_clear_passwords'] == 'n' ) $pass = '';

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
			($prefs['validateRegistration'] == 'y')? 'a': (($prefs['validateUsers'] == 'y')? 'u': NULL)
		    ));

	$this->assign_user_to_group($user, 'Registered');

	if( $prefs['eponymousGroups'] == 'y' )
	{
	    // Create a group just for this user, for permissions
	    // assignment.
	    $this->add_group($user, "Personal group for $user.",'',0,0,0,'');

	    $this->assign_user_to_group($user, $user);
	}
	
	$this->set_user_default_preferences($user);

	$cachelib->invalidate('userslist');
	return true;
    }
    
    function set_user_default_preferences($user) {
    	global $prefs;
	foreach( $prefs as $pref => $value ) {
		if ( ! ereg('^users_prefs_', $pref) ) continue;
		if ($pref == 'users_prefs_email_is_public') {
			$pref_name = 'email is public';
		} else {
			$pref_name = substr( $pref, 12 );
		}
		$this->set_user_preference($user, $pref_name, $value);
	}
    }

    function change_user_email($user, $email, $pass) {
    // Need to change the email-address for notifications, too
	global $notificationlib; include_once('lib/notifications/notificationlib.php');
    $oldMail = $this->get_user_email($user);
    $notificationlib->update_mail_address($oldMail, $email);
    
	$query = "update `users_users` set `email`=? where " . $this->convert_binary(). " `login`=?";

	$result = $this->query($query, array(
		    $email,
		    $user
		    ));

	// that block stays here for a time (compatibility)
	// lfagundes - only if pass is provided, admin doesn't need it
	// is this still necessary?
	if (!empty($pass)) {
	    $hash = $this->hash_pass($pass);
	    $query = "update `users_users` set `hash`=?  where " . $this->convert_binary(). " `login`=?";
	    $result = $this->query($query, array(
						 $hash,
						 $user
						 ));
	}

	$query = "update `tiki_user_watches` set `email`=? where " . $this->convert_binary(). " `user`=?";
	$result = $this->query($query, array( $email, $user));

	$query = "update `tiki_live_support_requests` set `email`=? where " . $this->convert_binary(). " `user`=?";
	$result = $this->query($query, array( $email, $user));
				return true;
    }

    function get_user_password($user) {
	$query = "select `password`,`provpass`  from `users_users` where " . $this->convert_binary(). " `login`=?";

	$result = $this->query($query, array($user));
	$res = $result->fetchRow();
	if (empty($res['provpass']))
		return $res['password'];
	else
		return $res['provpass'];
    }

    function get_user_email($user) {
    	global $prefs;
        return ( $prefs['login_is_email'] == 'y' && $user != 'admin' ) ? $user : $this->getOne("select `email` from `users_users` where " . $this->convert_binary(). " `login`=?", array($user));
    }

    /**
     *  Returns the contact users' email if set and permitted by Admin->Features settings
     */
    function get_admin_email() {
        global $user, $prefs, $tikilib;
        if (( !isset($user) && isset($prefs['contact_anon']) && $prefs['contact_anon'] == 'y' ) ||
                ( isset($user) && $user != '' && isset($prefs['feature_contact']) && $prefs['feature_contact'] == 'y' )) {
            return isset($prefs['sender_email']) ? $prefs['sender_email'] : $this->get_user_email($prefs['contact_user']);
        }
    }

    function get_user_hash($user) {
	$query = "select `hash`  from `users_users` where " .  $this->convert_binary(). " `login` = ?";
	$pass = $this->getOne($query, array($user));
	return $pass;
    }

    function get_user_by_hash($hash) {
	$query = "select `login` from `users_users` where `hash`=?";
	$pass = $this->getOne($query, array($hash));
	return $pass;
    }

	function create_user_cookie($user,$hash=false) {
		global $prefs;
		if (!$hash) {
			$hash = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']) . ".". ($this->now + $prefs['remembertime']);
		}
		$this->delete_user_cookie($user);
		$this->set_user_preference($user,'cookie',$hash);
		return $hash;
	}

	function delete_user_cookie($user) {
		$query = 'delete from `tiki_user_preferences` where `prefName`=? and `user`=?';
		$this->query($query, array('cookie',$user));
	}

	function get_user_by_cookie($hash,$bypasscheck=false) {
		list($check,$expire,$userCookie) = explode('.',$hash, 3);
		if ($check == md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']) or $bypasscheck) {
			$query = 'select `user` from `tiki_user_preferences` where `prefName`=? and `value` like ? and `user`=?';
			$user = $this->getOne($query, array('cookie',"$check.%",$userCookie));
			// $fp=fopen('temp/interlogtest','a+');fputs($fp,"main gubc -- $check.$expire.$userCookie -- $user --\n");fclose($fp);
			if ($user) {
				if ($expire < $this->now) {
					$query = 'delete from `tiki_user_preferences` where `prefName`=? and `value`=?';
					$user = $this->query($query, array('cookie',$hash));
					return false;
				} else {
					return $user;
				}
			}
		}
		return false;
	}

    function get_user_by_email($email) {
    $query = "select `login` from `users_users` where `email`=?";
    $pass = $this->getOne($query, array($email));
    return $pass;
    }
    
    function is_due($user) {
    	global $prefs, $phpcas_enabled;
    	// if CAS auth is enabled, don't check if password is due since CAS does not use local Tiki passwords
    	if (($phpcas_enabled == 'y' and $prefs['auth_method'] == 'cas') || $prefs['change_password'] != 'y') {
    		return false;
    	}
		$confirm = $this->getOne("select `pass_confirm`  from `users_users` where " . $this->convert_binary(). " `login`=?", array($user));
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
		$confirm = $this->getOne("select `email_confirm`  from `users_users` where " . $this->convert_binary(). " `login`=?", array($user));
		if ($confirm + (60 * 60 * 24 * $prefs['email_due']) < $this->now) {
		    return true;
		}
		return false;
    }

	function unsuccessful_logins($user) {
		return $this->getOne('select `unsuccessful_logins` from `users_users` where ' . $this->convert_binary(). ' `login`=?', array($user));
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
		$query = "select `provpass`  from `users_users` where `login`=?";
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
		global $prefs;

		//Validate password here
		if ( strlen($pass)<$prefs['min_pass_length'] ) {
			return tra("Password should be at least").' '.$prefs['min_pass_length'].' '.tra("characters long");
		}

		// Check this code
		if ($prefs['pass_chr_num'] == 'y') {
			if (!preg_match_all("/[0-9]+/", $pass, $foo) || !preg_match_all("/[A-Za-z]+/", $pass, $foo)) {
				return tra("Password must contain both letters and numbers");
			}
		}

		return "";
	}


    
    
    function change_user_password($user, $pass) {
	global $prefs;

	$hash = $this->hash_pass($pass);
	$new_pass_confirm = $this->now;

	if ($prefs['feature_clear_passwords'] == 'n') {
	    $pass = '';
	}

	$query = "update `users_users` set `hash`=? ,`password`=? ,`pass_confirm`=?, `provpass`=? where " . $this->convert_binary(). " `login`=?";
	$result = $this->query($query, array(
		    $hash,
		    $pass,
		    $new_pass_confirm,
		    "",
		    $user
		    ));
	// invalidate the cache so that after a fresh install, the admin (who has no user details at the install) can log in		
	global $cachelib; require_once('lib/cache/cachelib.php');
	$cachelib->invalidate('user_details_'.$user);
	return true;
	}

	function add_group($group, $desc, $home, $utracker=0, $gtracker=0, $rufields='', $userChoice='', $defcat=0, $theme='') {
		global $cachelib;  
		if ($this->group_exists($group))
			return false;
		$query = "insert into `users_groups`(`groupName`, `groupDesc`, `groupHome`,`groupDefCat`,`groupTheme`,`usersTrackerId`,`groupTrackerId`, `registrationUsersFieldIds`, `userChoice`) values(?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query, array($group, $desc, $home, $defcat, $theme, (int)$utracker, (int)$gtracker, $rufields, $userChoice) );
		$cachelib->invalidate('grouplist');
		return true;
	}

	function change_group($olgroup,$group,$desc,$home,$utracker=0,$gtracker=0,$ufield=0,$gfield=0,$rufields='',$userChoice='',$defcat=0,$theme='') {
		global $cachelib;
		if ( $olgroup == 'Anonymous' || $olgroup == 'Registered' ) {
			// Changing group name of 'Anonymous' and 'Registered' is not allowed.
			if ( $group != $olgroup ) return false;
		}
		if (!$this->group_exists($olgroup))
			return $this->add_group($group, $desc, $home, $utracker,$gtracker, $userChoice, $defcat, $theme);
		$query = "update `users_groups` set `groupName`=?, `groupDesc`=?, `groupHome`=?, ";
		$query .= "`groupDefCat`=?, `groupTheme`=?, ";
		$query.= " `usersTrackerId`=?, `groupTrackerId`=?, `usersFieldId`=?, `groupFieldId`=? , `registrationUsersFieldIds`=?, `userChoice`=? where `groupName`=?";
		$result = $this->query($query, array($group, $desc, $home, $defcat, $theme, (int)$utracker, (int)$gtracker, (int)$ufield, (int)$gfield, $rufields, $userChoice, $olgroup));
		$query = "update `users_usergroups` set `groupName`=? where `groupName`=?";
		$result = $this->query($query, array($group, $olgroup));
		$query = "update `users_grouppermissions` set `groupName`=? where `groupName`=?";
		$result = $this->query($query, array($group, $olgroup));
		$query = "update `users_objectpermissions` set `groupName`=? where `groupName`=?";
		$result = $this->query($query, array($group, $olgroup));
		$query = "update `tiki_group_inclusion` set `groupName`=? where `groupName`=?";
		$result = $this->query($query, array($group, $olgroup));
		$query = "update `tiki_group_inclusion` set `includeGroup`=? where `includeGroup`=?";
		$result = $this->query($query, array($group, $olgroup));
		$query = "update `tiki_newsreader_marks` set `groupName`=? where `groupName`=?";
		$result = $this->query($query, array($group, $olgroup));
		$query = "update `tiki_newsletter_groups` set `groupName`=? where `groupName`=?";
		$result = $this->query($query, array($group, $olgroup));
		
		// must unserialize before replacing the groups
		$query = "select `name`, `groups` from `tiki_modules` where `groups` like ?";
		$result = $this->query($query, array('%'.$olgroup.'%'));
		while ($res = $result->fetchRow()) {
			$aux = array();
			$aux["name"] = $res["name"];
			$aux["groups"] = unserialize($res["groups"]);
			$aux["groups"] = str_replace($olgroup, $group, $aux["groups"]);
			$aux["groups"] = serialize($aux["groups"]);
			$query = "update `tiki_modules` set `groups`=? where `name`=?";
			$this->query($query, array($aux["groups"],$aux["name"]));
		}

		$cachelib->invalidate('grouplist');
		return true;
	}

    function remove_all_inclusions($group) {
	if (!$this->group_exists($group))
	    return false;

	$query = "delete from `tiki_group_inclusion` where `groupName` = ?";
	$result = $this->query($query, array($group));
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
	    
	if (sizeof($q) > 0) {
	    $query = "update `users_users` set " . implode(",", $q). " where " .
		$this->convert_binary(). " `login` = ?";
	    $bindvars[] = $u['login'];
	    $result = $this->query($query, $bindvars);
	}

	$aUserPrefs = array('realName','homePage','country');
	foreach ($aUserPrefs as $pref){
		if (isset($u[$pref])) {
		    $bindvars = array();

		    $bindvars[] = strip_tags($u[$pref]);
		    $bindvars[] = $u['login'];
		    $bindvars[] = $pref;

		    if ($this->getOne("select `user` from `tiki_user_preferences` where `user`=? and `prefName`=?",array($u['login'],$pref))) {
			$query = "UPDATE `tiki_user_preferences` set `value`=? where `user`=? and `prefName`=?";
		    } else {
			$query = "INSERT INTO `tiki_user_preferences` (`value`,`user`,`prefName`) VALUES (?,?,?)";
		    }
		    $this->query($query, $bindvars);
		}
	}

	return $result;
    }

    // damian aka damosoft
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
	    
	$result = $this->query($query, $bindvals, $max, 0);
	    
	$ret = array();
	while ($row = $result->fetchRow()) {
	    $ret[] = $row;
	}
	    
	return $ret;
    }

    // Friends methods
    function request_friendship($user, $friend)
    {
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

    function accept_friendship($user, $friend)
    {
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

    function refuse_friendship($user, $friend)
    {
		$exists = $this->getOne("select count(*) from `tiki_friendship_requests` where `userTo`=? and `userFrom`=?",
					array($user, $friend));
	
		if (!$exists)
		    return false;
	
		$query = "delete from `tiki_friendship_requests` where `userFrom`=? and `userTo`=?";
		$this->query($query, array($user, $friend));
		$this->query($query, array($friend, $user));
	
		return true;
    }
    
    function list_pending_friendship_requests($user)
    {
	
		$query = "select * from `tiki_friendship_requests` where `userTo`=? order by tstamp";
		$result = $this->query($query, array($user));
	
		$requests = array();
		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
		    $requests[$res['userFrom']] = $res['tstamp'];
		}
	
		return $requests;
    }

    function list_waiting_friendship_requests($user)
    {
		$query = "select * from `tiki_friendship_requests` where `userFrom`=? order by tstamp";
		$result = $this->query($query, array($user));
	
		$requests = array();
		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
		    $requests[$res['userTo']] = $res['tstamp'];
		}
	
		return $requests;
    }



    function break_friendship($user, $friend)
    {

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
		$result = $this->query($query, array(md5($objectType . $objectId),$objectType));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}

	function object_has_one_case_permission($objectId, $objectType) {
		$query = "select count(*) from `users_objectpermissions` where `objectId`=? and `objectType`=?";
		$result = $this->getOne($query, array( md5($objectType . $objectId), $objectType));
		return $result;
	}

	function remove_object_case_permission($groupName, $objectId, $objectType, $permName) {
		$query = "delete from `users_objectpermissions` where `groupName` = ? and `objectId` = ?  and `objectType` = ? and `permName` = ?";
		$result = $this->query($query, array($groupName, md5($objectType . $objectId), $objectType, $permName));
		return true;
	}

	function get_permissions_types() {
		$query = "select `type` from `users_permissions` group by `type`";
		$result = $this->query($query,array());
		$ret = array();
		while ($res = $result->fetchRow()) { $ret[] = $res['type']; }
		return $ret;									
	}
	function send_validation_email($name, $apass, $email, $again='', $second='') {
		global $tikilib, $prefs, $smarty;
		$foo = parse_url($_SERVER['REQUEST_URI']);
		$foo1 = str_replace('tiki-register', 'tiki-login_validate',$foo['path']);
		$foo1 = str_replace('tiki-remind_password', 'tiki-login_validate',$foo1);
		$machine = $tikilib->httpPrefix().$foo1;
		$smarty->assign('mail_machine',$machine);
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
		} elseif ($prefs['validateRegistration'] == 'y') {
			$mail_data = $smarty->fetch('mail/moderate_validation_mail.tpl');
			$mail_subject = $smarty->fetch('mail/moderate_validation_mail_subject.tpl');
			if ($prefs['sender_email'] == NULL or !$prefs['sender_email']) {
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
				if (!$mail->send(array($prefs['sender_email']))) {
					$smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
					return false;
				} elseif (empty($again)) {
					$smarty->assign('msg', $smarty->fetch('mail/user_validation_waiting_msg.tpl'));
				} else {
					$smarty->assign('msg', tra('The administrator has not yet validated your account. Please wait.'));
				}
			}
		} elseif ($prefs['validateUsers'] == 'y') {
			$mail_data = $smarty->fetch('mail/user_validation_mail.tpl');
			$mail = new TikiMail();
			$mail->setText($mail_data);
			$mail_data = $smarty->fetch('mail/user_validation_mail_subject.tpl');
			$mail->setSubject($mail_data);
			if (!$mail->send(array($email))) {
				$smarty->assign('msg', tra("The registration mail can't be sent. Contact the administrator"));
				return false;
			} elseif (empty($again)) {
				$smarty->assign('msg',$smarty->fetch('mail/user_validation_msg.tpl'));
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

	function confirm_email($user, $pass) {
		global $prefs, $tikilib;
		$query = 'select `provpass`, `login`, `unsuccessful_logins` from `users_users` where `login`=?';
		$result = $this->query($query, array($user));
		if (!($res = $result->fetchRow())) {
			return false;
		}
		if (md5($res['provpass']) == $pass){
			$query = 'update `users_users` set `provpass`=?, `email_confirm`=?, `unsuccessful_logins`=? where `login`=? and `provpass`=?';
			$this->query($query, array('', $tikilib->now, 0, $user, $res['provpass']));
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
		$smarty->assign('user', $user);
		$mail = new TikiMail();
		$mail_data = $smarty->fetchLang($languageEmail, "mail/$tpl"."_subject.tpl");
		$mail_data = sprintf($mail_data, $_SERVER['SERVER_NAME']);
		$mail->setSubject($mail_data);
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$mail_machine = $tikilib->httpPrefix().str_replace('tiki-login.php', 'tiki-confirm_user_email.php', $foo['path']);
		$smarty->assign('mail_machine', $mail_machine);
		$mail_data = $smarty->fetchLang($languageEmail, "mail/$tpl.tpl");		
		$mail->setText($mail_data);
		if (!$mail->send(array($this->get_user_email($user)))) {
			$smarty->assign('msg', tra("The user email confirmation can't be sent. Contact the administrator"));
		} else {
			$smarty->assign('msg', 'It is time to confirm your email. You will receive an mail with the instruction to follow');
		}
	}

	function assign_openid( $username, $openid ) {
		// This won't update the database unless the openid is different
		$this->query("UPDATE `users_users` SET openid_url = ? WHERE login = ? AND ( openid_url <> ? OR openid_url IS NULL )", array( $openid, $username, $openid ));
	}

	function intervalidate($remote,$user,$pass,$get_info = false) {
		global $prefs;
		include_once('XML/RPC.php');
		$hashkey = md5($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']) . ".". ($this->now + $prefs['remembertime']);
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
		// $fp=fopen('temp/interlogtest','a+');fputs($fp,"slave     -- ".$hash." --\n");fclose($fp);
		$client = new XML_RPC_Client($remote['path'], $remote['host'], $remote['port']);
		$client->setDebug(0);
		$msg = new XML_RPC_Message(
		       'intertiki.cookiecheck',
					 array(
					 new XML_RPC_Value($prefs['tiki_key'], 'string'),
					 new XML_RPC_Value($hash, 'string')
					 ));
		$result = $client->send($msg);
		return $result;
	}

}

/* For the emacs weenies in the crowd.
Local Variables:
   c-basic-offset: 4
End:
*/

