<?php

// Lib for user administration, groups and permissions
// This lib uses pear so the constructor requieres
// a pear DB object

// some definitions for helping with authentication
define("USER_VALID", 2);

define("SERVER_ERROR", -1);
define("PASSWORD_INCORRECT", -3);
define("USER_NOT_FOUND", -5);
define("ACCOUNT_DISABLED", -6);

class UsersLib extends TikiLib {
	# var $db;  // The PEAR db object used to access the database

	// change this to an email address to receive debug emails from the LDAP code
	var $debug = false;

	var $usergroups_cache;
	var $groupperm_cache;

	function UsersLib($db) {
		$this->db = $db;

		// Initialize caches
		$this->usergroups_cache = array();
		$this->groupperm_cache = array(array());
	}

	function set_admin_pass($pass) {
		global $feature_clear_passwords;

		$query = "select email from users_users where login='admin'";
		$email = $this->getOne($query);
		$hash = md5("admin" . $pass . $email);

		if ($feature_clear_passwords == 'n')
			$pass = '';

		$query = "update `users_users` set `password`='$pass',hash='$hash' where `login`='admin'";
		$result = $this->query($query);
		return true;
	}

	function assign_object_permission($groupName, $objectId, $objectType, $permName) {
		$groupName = addslashes($groupName);

		$objectId = md5($objectType . $objectId);
		$query = "replace into users_objectpermissions(groupName,objectId,objectType,permName) values('$groupName','$objectId','$objectType','$permName')";
		$result = $this->query($query);
		return true;
	}

	function object_has_permission($user, $objectId, $objectType, $permName) {
		$groups = $this->get_user_groups($user);

		$objectId = md5($objectType . $objectId);

		foreach ($groups as $groupName) {
			$query = "select `permName`  from `users_objectpermissions` where `groupName`='$groupName' and objectId='$objectId' and objectType='$objectType' and permName = '$permName'";

			$result = $this->query($query);

			if ($result->numRows())
				return true;
		}

		return false;
	}

	function remove_object_permission($groupName, $objectId, $objectType, $permName) {
		$objectId = md5($objectType . $objectId);

		$query = "delete from `users_objectpermissions` where `groupName`='$groupName' and objectId='$objectId' and objectType='$objectType' and permName='$permName'";
		$result = $this->query($query);
		return true;
	}

	function copy_object_permissions($objectId,$destinationObjectId,$objectType) {
		$objectId = md5($objectType.$objectId);

		$query = "select permName, groupName from users_objectpermissions where objectId='$objectId' and  objectType='$objectType'";
    		$result = $this->query($query);
    		while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      			$this->assign_object_permission($res["groupName"],$destinationObjectId,$objectType,$res["permName"]);
    		}
    		return true;
  	}

	function get_object_permissions($objectId, $objectType) {
		$objectId = md5($objectType . $objectId);

		$query = "select `groupName` ,permName from `users_objectpermissions` where `objectId`='$objectId' and objectType='$objectType'";
		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function object_has_one_permission($objectId, $objectType) {
		$objectId = md5($objectType . $objectId);

		$query = "select `objectId`,`objectType` from `users_objectpermissions` where `objectId`=? and `objectType`=?";
		$result = $this->query($query, array(
			$objectId,
			$objectType
		));

		return $result->numRows();
	}

	function user_exists($user) {
		static $rv = array();

		if (!isset($rv[$user])) {
			$query = "select `login` from `users_users` where `login` = ?";

			$result = $this->query($query, array($user));
			$rv[$user] = $result->numRows();
		}

		return $rv[$user];
	}

	function group_exists($group) {
		static $rv = array();

		if (!isset($rv[$group])) {
			$query = "select `groupName`  from `users_groups` where `groupName` = ?";

			$result = $this->query($query, array($group));
			$rv[$group] = $result->numRows();
		}

		return $rv[$group];
	}

	function user_logout($user) {
		$t = date("U");
	// No need to change lastLogin since it is handled at the validateUser method
	//$query = "update `users_users` set `lastLogin`=$t where `login`='$user'";
	//$result = $this->query($query);
	}

	function genPass() {
		// AWC: enable mixed case and digits, don't return too short password
		global $min_pass_length;                                          //AWC

		$vocales = "AaEeIiOoUu13580";                                     //AWC
		$consonantes = "BbCcDdFfGgHhJjKkLlMmNnPpQqRrSsTtVvWwXxYyZz24679"; //AWC
		$r = '';
		$passlen = ($min_pass_length > 5) ? $min_pass_length : 5;         //AWC

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
			"select count(*) from `users_users` where " . $this->convert_binary(). " `login` = '$user' and hash='$hash'");
	}

	function validate_user($user, $pass, $challenge, $response) {
		global $tikilib, $sender_email;

		// these will help us keep tabs of what is going on
		$userTiki = false;
		$userTikiPresent = false;
		$userAuth = false;
		$userAuthPresent = false;

		// see if we are to use PEAR::Auth
		$auth_pear = ($tikilib->get_preference("auth_method", "tiki") == "auth");
		$create_tiki = ($tikilib->get_preference("auth_create_user_tiki", "n") == "y");
		$create_auth = ($tikilib->get_preference("auth_create_user_auth", "n") == "y");
		$skip_admin = ($tikilib->get_preference("auth_skip_admin", "n") == "y");

		// first attempt a login via the standard Tiki system
		$result = $this->validate_user_tiki($user, $pass, $challenge, $response);

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
		if (!$auth_pear || ($auth_pear && $user == "admin" && $skip_admin)) {
			// if the user verified ok, log them in
			if ($userTiki)
				return $this->update_lastlogin($user);
			// if the user password was incorrect but the account was there, give an error
			elseif ($userTikiPresent)
				return false;
			// if the user was not found, give an error
			// this could be for future uses
			else
				return false;
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
				return $this->update_lastlogin($user);
			}
				// if the user wasn't found in either system, just fail
				elseif (!$userTikiPresent && !$userAuthPresent) {
				return false;
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
						return $this->update_lastlogin($user);
						// if the server didn't work, do something!
						elseif ($result == SERVER_ERROR) {
						// check the notification status for this type of error
						return false;
					}
						// otherwise don't log in.
						else
						return false;
				}
					// otherwise
					else
					// just say no!
					return false;
			}

				// if the user was logged into Auth but not found in Tiki
				elseif ($userAuth && !$userTikiPresent) {
				// see if we can create a new account
				if ($create_tiki) {
					// need to make this better! *********************************************************
					$result = $this->add_user($user, $pass, '');

					// if it worked ok, just log in
					if ($result == USER_VALID)
						// before we log in, update the login counter
						return $this->update_lastlogin($user);
						// if the server didn't work, do something!
						elseif ($result == SERVER_ERROR) {
						// check the notification status for this type of error
						return false;
					}
						// otherwise don't log in.
						else
						return false;
				}
					// otherwise
					else
					// just say no!
					return false;
			}
				// if the user was logged into Auth and found in Tiki (no password in Tiki user table necessary)
				elseif ($userAuth && $userTikiPresent)
				return true;
		}

		// we will never get here
		return false;
	}

	// validate the user in the PEAR::Auth system
	function validate_user_auth($user, $pass) {
		global $tikilib;

		include_once ("Auth/Auth.php");

		// just make sure we're supposed to be here
		if ($tikilib->get_preference("auth_method", "tiki") != "auth")
			return false;

		// get all of the LDAP options from the database
		$options["host"] = $tikilib->get_preference("auth_ldap_host", "localhost");
		$options["port"] = $tikilib->get_preference("auth_ldap_port", "389");
		$options["scope"] = $tikilib->get_preference("auth_ldap_scope", "sub");
		$options["basedn"] = $tikilib->get_preference("auth_ldap_basedn", "");
		$options["userdn"] = $tikilib->get_preference("auth_ldap_userdn", "");
		$options["userattr"] = $tikilib->get_preference("auth_ldap_userattr", "uid");
		$options["useroc"] = $tikilib->get_preference("auth_ldap_useroc", "posixAccount");
		$options["groupdn"] = $tikilib->get_preference("auth_ldap_groupdn", "");
		$options["groupattr"] = $tikilib->get_preference("auth_ldap_groupattr", "cn");
		$options["groupoc"] = $tikilib->get_preference("auth_ldap_groupoc", "groupOfUniqueNames");
		$options["memberattr"] = $tikilib->get_preference("auth_ldap_memberattr", "uniqueMember");
		$options["memberisdn"] = ($tikilib->get_preference("auth_ldap_memberisdn", "y") == "y");

		// set the Auth options
		$a = new Auth("LDAP", $options, "", false, $user, $pass);
		// turn off the error message
		$a->setShowLogin(false);
		$a->start();
		$status = "";

		// check if the login correct
		if ($a->getAuth())
			$status = USER_VALID;

		// otherwise use the error status given back
		else
			$status = $a->getStatus();

		if ($this->debug != false) {
			$msg = "Status: " . $status . "\n";

			foreach ($options as $key => $val)
				$msg .= "$key = $val\n";

			mail($this->debug, "testing auth", $msg, "From: $sender_email");
		}

		return $status;
	}

	// validate the user in the Tiki database
	function validate_user_tiki($user, $pass, $challenge, $response) {
		// If the user is loggin in the the lastLogin should be the last currentLogin?
		global $feature_challenge;

		$user = addslashes($user);

		// first verify that the user exists
		$query = "select `email` from `users_users` where " . $this->convert_binary(). " `login` = ?";
		$result = $this->query($query, array($user));

		if (!$result->numRows())
			return USER_NOT_FOUND;

		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$hash=md5($user.$pass.$res['email']);
		$hash2 = md5($pass);

		// next verify the password with 2 hashes methods, the old one (passà)) and the new one (login.pass;email)
		if ($feature_challenge == 'n' || empty($response)) {
			$query
				= "select `login` from `users_users` where " . $this->convert_binary(). " `login` = ? and (`hash`=? or `hash`=?)";

			$result = $this->query($query, array(
				$user,
				$hash,
				$hash2
			));

			if ($result->numRows()) {
				$t = date("U");

				// Check
				$current = $this->getOne("select `currentLogin` from `users_users` where `login`=?", array($user));

				if (is_null($current)) {
					// First time
					$current = $t;
				}

				$query = "update `users_users` set `lastLogin`=? where `login`=?";
				$result = $this->query($query, array(
					(int)$current,
					$user
				));
				// check
				$query = "update `users_users` set `currentLogin`=? where `login`=?";
				$result = $this->query($query, array(
					(int)$t,
					$user
				));

				return true;
			}
		} else {
			// Use challenge-reponse method
			// Compare pass against md5(user,challenge,hash)
			$hash = $this->getOne("select `hash`  from `users_users` where " . $this->convert_binary(). " `login`='$user'");

			if (!isset($_SESSION["challenge"]))
				return false;

			//print("pass: $pass user: $user hash: $hash <br/>");
			//print("challenge: ".$_SESSION["challenge"]." challenge: $challenge<br/>");
			//print("response : $response<br/>");
			if ($response == md5($user . $hash . $_SESSION["challenge"])) {
				$t = date("U");

				// Check
				$current = $this->getOne("select `currentLogin` from `users_users` where `login`=?", array($user));

				if (is_null($current)) {
					// First time
					$current = $t;
				}

				$query = "update `users_users` set `lastLogin`=? where `login`=?";
				$result = $this->query($query, array(
					(int)$current,
					$user
				));

				// check
				$query = "update `users_users` set `currentLogin`=? where `login`=?";
				$result = $this->query($query, array(
					(int)$t,
					$user
				));

				return true;
			} else {
				return false;
			}
		}

		return PASSWORD_INCORRECT;
	}

	// update the lastlogin status on this user
	function update_lastlogin($user) {
		$t = date("U");

		// Check
		$current = $this->getOne("select `currentLogin` from `users_users` where `login`= ?", array($user));

		if (is_null($current)) {
			// First time
			$current = $t;
		}

		$query = "update `users_users` set `lastLogin`=? where `login`=?";
		$result = $this->query($query, array(
			(int)$current,
			$user
		));

		// check
		$query = "update `users_users` set `currentLogin`=? where `login`=?";
		$result = $this->query($query, array(
			(int)$t,
			$user
		));

		return true;
	}

	// create a new user in the Auth directory
	function create_user_auth($user, $pass) {
		global $tikilib, $sender_email;

		$options = array();
		$options["host"] = $tikilib->get_preference("auth_ldap_host", "localhost");
		$options["port"] = $tikilib->get_preference("auth_ldap_port", "389");
		$options["scope"] = $tikilib->get_preference("auth_ldap_scope", "sub");
		$options["basedn"] = $tikilib->get_preference("auth_ldap_basedn", "");
		$options["userdn"] = $tikilib->get_preference("auth_ldap_userdn", "");
		$options["userattr"] = $tikilib->get_preference("auth_ldap_userattr", "uid");
		$options["useroc"] = $tikilib->get_preference("auth_ldap_useroc", "posixAccount");
		$options["groupdn"] = $tikilib->get_preference("auth_ldap_groupdn", "");
		$options["groupattr"] = $tikilib->get_preference("auth_ldap_groupattr", "cn");
		$options["groupoc"] = $tikilib->get_preference("auth_ldap_groupoc", "groupOfUniqueNames");
		$options["memberattr"] = $tikilib->get_preference("auth_ldap_memberattr", "uniqueMember");
		$options["memberisdn"] = ($tikilib->get_preference("auth_ldap_memberisdn", "y") == "y");
		$options["adminuser"] = $tikilib->get_preference("auth_ldap_adminuser", "");
		$options["adminpass"] = $tikilib->get_preference("auth_ldap_adminpass", "");

		// set additional attributes here
		$userattr = array();
		$userattr["email"] = $this->getOne("select `email` from `users_users` where `login`='$user'");

		// set the Auth options
		$a = new Auth("LDAP", $options);

		// check if the login correct
		if ($a->addUser($user, $pass, $userattr) === true)
			$status = USER_VALID;

		// otherwise use the error status given back
		else
			$status = $a->getStatus();

		// if we're in debug mode, send an email
		if ($this->debug) {
			$msg = "Status: " . $status . "\n";

			foreach ($options as $key => $val)
				$msg .= "$key = $val\n";

			if ($this->debug != false)
				mail($this->debug, "create_user_auth", $msg, "From: $sender_email");
		}

		return $status;
	}

	function get_users_names($offset = 0, $maxRecords = -1, $sort_mode = 'login_desc', $find = '') {

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

	function get_users($offset = 0, $maxRecords = -1, $sort_mode = 'login_desc', $find = '') {
		$sort_mode = str_replace("_", " ", $sort_mode);

		// Return an array of users indicating name, email, last changed pages, versions, lastLogin 
		if ($find) {
			$mid = " where `login` like '%" . $find . "%'";
		} else {
			$mid = '';
		}

		$query = "select * from `users_users` $mid order by $sort_mode limit $offset,$maxRecords";

		$query_cant = "select count(*) from users_users";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["user"] = $res["login"];
			$user = $aux["user"];
			$aux["email"] = $res["email"];
			$aux["lastLogin"] = $res["lastLogin"];
			$groups = $this->get_user_groups($user);
			$aux["groups"] = $groups;
			$aux["currentLogin"] = $res["currentLogin"];
			$ret[] = $aux;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function group_inclusion($group, $include) {
		$query = "replace into tiki_group_inclusion(groupName,includeGroup) values('$group','$include')";

		$result = $this->query($query);
	}

	function get_included_groups($group) {
		$query = "select `includeGroup`  from `tiki_group_inclusion` where `groupName`=?";

		$result = $this->query($query, array($group));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res["includeGroup"];

			$ret2 = $this->get_included_groups($res["includeGroup"]);
			$ret = array_merge($ret, $ret2);
		}

		return array_unique($ret);
	}

	function remove_user_from_group($user, $group) {
		$userid = $this->get_user_id($user);

		$query = "delete from `users_usergroups` where `userId`=$userid and groupName='$group'";
		$result = $this->query($query);
	}

	function get_groups($offset = 0, $maxRecords = -1, $sort_mode = 'groupName_desc', $find = '') {
		$sort_mode = $this->convert_sortmode($sort_mode);

		// Return an array of users indicating name, email, last changed pages, versions, lastLogin 
		if ($find) {
			$mid = " where `groupName` like ?";

			$bindvars[] = "%" . $find . "%";
			$findesc = $this->qstr('%' . $find . '%');
			$mid = " where groupName like $findesc";
		} else {
			$mid = '';

			$bindvars = false;
		}

		$query = "select `groupName` , `groupDesc` from `users_groups` $mid order by $sort_mode";
		$query_cant = "select count(*) from `users_groups`";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, false);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["groupName"] = $res["groupName"];
			$aux["groupDesc"] = $res["groupDesc"];
			$perms = $this->get_group_permissions($aux["groupName"]);
			$aux["perms"] = $perms;
			$groups = $this->get_included_groups($aux["groupName"]);
			$aux["included"] = $groups;
			$ret[] = $aux;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_user_id($user) {
		$id = $this->getOne("select `userId` from `users_users` where `login`=?", array($user));

		$id = ($id === NULL) ? -1 : $id;
		return $id;
	}

	function remove_user($user) {
		$userId = $this->getOne("select `userId`  from `users_users` where `login` = '$user'");

		$query = "delete from `users_users` where `login` = '$user'";
		$result = $this->query($query);
		$query = "delete from `users_usergroups` where `userId`=$userId";
		$result = $this->query($query);

		return true;
	}

	function remove_group($group) {
		$query = "delete from `users_groups` where `groupName` = '$group'";

		$result = $this->query($query);
		$query = "delete from `tiki_group_inclusion` where `groupName` = '$group' or includeGroup='$group'";
		$result = $this->query($query);
		$query = "delete from `users_grouppermissions` where `groupName`='$group'";
		$result = $this->query($query);
		return true;
	}

	function get_user_groups($user) {
		if (!isset($this->usergroups_cache[$user])) {
			$userid = $this->get_user_id($user);

			$query = "select `groupName` from `users_usergroups` where `userId`=?";
			$result = $this->query($query, array($userid));
			$ret = array();

			while ($res = $result->fetchRow()) {
				$ret[] = $res["groupName"];

				$included = $this->get_included_groups($res["groupName"]);
				$ret = array_merge($ret, $included);
			}

			$ret[] = "Anonymous";
			$ret = array_unique($ret);
			// cache it
			$this->usergroups_cache[$user] = $ret;
			return $ret;
		} else {
			return $this->usergroups_cache[$user];
		}
	}
	
	function get_user_default_group($user) {
		$query = "select default_group from users_users where login='$user'";
		$result = $this->query($query);
		if($result->numRows()) {
			$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
			$ret = $res['default_group'];
		} else {
			$ret ='';
		}
		return $ret;
	}
  
	function get_group_home($group) {
		$query = "select `groupHome` from `users_groups` where `groupName`=?";
		$result = $this->query($query,array($group));
		if($result->numRows()) {
			$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
			$ret = $res['groupHome'];
		} else {
			$ret ='';
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

	function get_user_info($user) {
		$query = "select * from `users_users` where `login`=?";
		$result = $this->query($query,array($user));
		$res = $result->fetchRow();
		$aux = array();
		foreach ($res as $key => $val) {
			$aux[$key] = $val;
		}
		$groups = $this->get_user_groups($user);
		$res["groups"] = $groups;
		return $res;
	}

	function set_default_group($user,$group) {
		$groupesc=$this->qstr($group);
		$query = "update users_users set default_group=$groupesc where login='$user'";
		$this->query($query);
	}
  
	function batch_set_default_group($group) {
		$users = $this->get_group_users($group);
		foreach ($users as $user) {
		$this->set_default_group($user,$group);
		}
	}

	function change_permission_level($perm, $level) {
		$level = addslashes($level);

		$query = "update `users_permissions` set `level`='$level' where `permName`='$perm'";
		$this->query($query);
	}

	function assign_level_permissions($group, $level) {
		$query = "select `permName`  from `users_permissions` where `level`='$level'";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$this->assign_permission_to_group($res['permName'], $group);
		}
	}

	function remove_level_permissions($group, $level) {
		$query = "select `permName`  from `users_permissions` where `level`='$level'";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$this->remove_permission_from_group($res['permName'], $group);
		}
	}

	function create_dummy_level($level) {
		$query = "replace into users_permissions(permName,permDesc,type,level) values('','','','$level')";

		$this->query($query);
	}

	function get_permission_levels() {
		$query = "select distinct (level) from users_permissions";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res['level'];
		}

		return $ret;
	}

	function get_userid_info($user) {
		$query = "select * from `users_users` where `userId`='$user'";

		$result = $this->query($query);
		$res = $result->fetchRow();
		$aux = array();

		foreach ($res as $key => $val) {
			$aux[$key] = $val;
		}

		$groups = $this->get_user_groups($user);
		$res["groups"] = $groups;
		return $res;
	}

	function get_permissions($offset = 0, $maxRecords = -1, $sort_mode = 'permName_desc', $find = '', $type = '', $group = '') {
		$values = array();

		$sort_mode = $this->convert_sortmode($sort_mode);

		$mid = '';

		if ($type) {
			$mid = ' where `type`= ? ';

			$values[] = $type;
		}

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			if ($mid) {
				$mid .= " and `permName` like '%?%'";

				$values[] = $find;
			} else {
				$mid .= " where `permName` like '%?%'";

				$values[] = $find;
			}
		}

		$query = "select `permName`,`type`,`level`,`permDesc` from `users_permissions` $mid order by $sort_mode ";

		#	$query_cant = "select count(*) from `users_permissions` $mid";
		$result = $this->query($query, $values, $maxRecords, $offset);
		#	$cant = $this->getOne($query_cant, $values);
		$cant = 0;
		$ret = array();

		while ($res = $result->fetchRow()) {
			if ($group && $this->group_has_permission($group, $res['permName'])) {
				$hasPerm = 'y';
			} else {
				$hasPerm = 'n';
			}

			$ret[] = array(
				'permName' => $res['permName'],
				'permDesc' => $res['permDesc'],
				'type' => $res['type'],
				'level' => $res['level'],
				'hashPerm' => $hasPerm,
			);
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

	function assign_permission_to_group($perm, $group) {
		$group = addslashes($group);

		$query = "replace into users_grouppermissions(groupName,permName) values('$group','$perm')";
		$result = $this->query($query);
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
		if (!isset($perm, $this->groupperm_cache[$group][$perm])) {
			$query = "select `groupName` ,`permName` from `users_grouppermissions` where `groupName`=? and `permName`=?";

			$result = $this->query($query, array(
				$group,
				$perm
			));

			$this->groupperm_cache[$group][$perm] = $result->numRows();
			return $result->numRows();
		} else {
			return $this->groupperm_cache[$group][$perm];
		}
	}

	function remove_permission_from_group($perm, $group) {
		$query = "delete from `users_grouppermissions` where `permName`='$perm' and groupName= '$group'";

		$result = $this->query($query);
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
		$userid = $this->get_user_id($user);

		$query = "insert into `users_usergroups`(`userId`,`groupName`) values(?,?)";
		$result = $this->query($query, array(
			$userid,
			$group
		), -1, -1, false);

		return true;
	}

	function confirm_user($user) {
		global $feature_clear_passwords;

		$query = "select `provpass`, `login`, `email` from `users_users` where `login`=?";
		$result = $this->query($query, array($user));
		$res = $result->fetchRow();
		$hash = md5($res["login"] . $res["provpass"] . $res["email"]);
		$provpass = $res["provpass"];

		if ($feature_clear_passwords == 'n') {
			$provpass = '';
		}

		$query = "update `users_users` set `password`=? ,`hash`=? ,`provpass`=? where `login`=?";
		$result = $this->query($query, array(
			$provpass,
			$hash,
			'',
			$user
		));
	}

	function add_user($user, $pass, $email, $provpass = '') {
		global $pass_due;

		global $feature_clear_passwords;
		// Generate a unique hash; this is also done below in set_user_fields()
		$hash = md5($user . $pass . $email);

		if ($feature_clear_passwords == 'n')
			$pass = '';

		if ($this->user_exists($user))
			return false;

		$now = date("U");
		$new_pass_due = $now + (60 * 60 * 24 * $pass_due);
		$query = "insert into `users_users`(`login`,`password`,`email`,`provpass`,`registrationDate`,`hash`,`pass_due`,`created`) values(?,?,?,?,?,?,?,?)";
		$result = $this->query($query, array(
			$user,
			$pass,
			$email,
			$provpass,
			(int) $now,
			$hash,
			(int) $new_pass_due,
			(int) $now
		));

		$this->assign_user_to_group($user, 'Registered');
		return true;
	}

	function change_user_email($user, $email, $pass) {
		$query = "update `users_users` set `email`=? where " . $this->convert_binary(). " `login`=?";

		$result = $this->query($query, array(
			$email,
			$user
		));

		$hash = md5($user . $pass . $email);
		$query = "update `users_users` set `hash`=? where " . $this->convert_binary(). " `login`=?";
		$result = $this->query($query, array(
			$hash,
			$user
		));

		$query = "update `tiki_user_watches` set `email`=? where " . $this->convert_binary(). " `user`=?";
		$result = $this->query($query, array(
			$email,
			$user
		));
	}

	function get_user_password($user) {
		$query = "select `password`  from `users_users` where " . $this->convert_binary(). " `login`=?";

		$pass = $this->getOne($query, array($user));
		return $pass;
	}

	function get_user_hash($user) {
		$query = "select `hash`  from `users_users` where " . $this->convert_binary(). " `login`='$user'";

		$pass = $this->getOne($query);
		return $pass;
	}

	function get_user_by_hash($hash) {
		$query = "select `login` from `users_users` where `hash`=?";

		$pass = $this->getOne($query, array($hash));
		return $pass;
	}

	function is_due($user) {
		$due = $this->getOne("select `pass_due`  from `users_users` where " . $this->convert_binary(). " `login`=?", array($user));

		if ($due <= date("U"))
			return true;

		return false;
	}

	function renew_user_password($user) {
		$pass = $this->genPass();
		$query = "select email from users_users where login='$user'";
		$email = $this->getOne($query);
		$hash = md5($user . $pass . $email);
		// Note that tiki-generated passwords are due inmediatley
		$now = date("U");
		$query = "update `users_users` set `password`='$pass', hash='$hash',pass_due=$now where " . $this->convert_binary(). " `login`='$user'";
		$result = $this->query($query);
		return $pass;
	}

	function change_user_password($user, $pass) {
		global $pass_due;

		global $feature_clear_passwords;
		$query = "select email from users_users where login='$user'";
		$email = $this->getOne($query);
		$hash = md5($user . $pass . $email);
		$now = date("U");
		$new_pass_due = $now + (60 * 60 * 24 * $pass_due);

		if ($feature_clear_passwords == 'n') {
			$pass = '';
		}

		$query = "update `users_users` set `hash`=? ,`password`=? ,`pass_due`=? where " . $this->convert_binary(). " `login`=?";
		$result = $this->query($query, array(
			$hash,
			$pass,
			$new_pass_due,
			$user
		));
	}

	function add_group($group, $desc, $home) {
		if ($this->group_exists($group))
			return false;

		$group = addslashes($group);
		$desc = addslashes($desc);
		$query = "insert into users_groups(groupName, groupDesc, groupHome) values('$group','$desc', '$home')";
		$result = $this->query($query);
		return true;
	}

	function change_group($olgroup,$group,$desc,$home) {
		if (!$this->group_exists($olgroup))
			return $this->add_group($group, $desc, $home);

		$query = "update `users_groups` set `groupName`='$group', groupDesc='$desc', groupHome='$home' where `groupName`='$olgroup'";
		$result = $this->query($query);
		$query = "update `users_usergroups` set `groupName`='$group' where `groupName`='$olgroup'";
		$result = $this->query($query);
		$query = "update `users_grouppermissions` set `groupName`='$group' where `groupName`='$olgroup'";
		$result = $this->query($query);
		$query = "update `users_objectpermissions` set `groupName`='$group' where `groupName`='$olgroup'";
		$result = $this->query($query);
		$query = "update `tiki_group_inclusion` set `groupName`='$group' where `groupName`='$olgroup'";
		$result = $this->query($query);
		$query = "update `tiki_newsreader_marks` set `groupName`='$group' where `groupName`='$olgroup'";
		$result = $this->query($query);
		$query = "update `tiki_modules` set `groups`=replace(groups,'$olgroup','$group') where `groups` like '%$olgroup%'";
		$result = $this->query($query);
		return true;
	}

	function remove_all_inclusions($group) {
		if (!$this->group_exists($group))
			return false;

		$query = "delete from `tiki_group_inclusion` where `groupName`='$group'";
		$result = $this->query($query);
		return true;
	}

	function set_user_fields($u) {
		global $feature_clear_passwords;

		if (@$u['password']) {
			if ($feature_clear_passwords == 's') {
				$q[] = "password='" . addslashes(strip_tags($u['password'])). "'";
			}

			// I don't think there are currently cases where login and email are undefined
			$hash = md5($u['login'] . $u['password'] . $u['email']);
			$q[] = "hash='$hash'";
		}

		if (@$u['email']) {
			$q[] = "email='" . addslashes(strip_tags($u['email'])). "'";
		}

		if (@$u['realname']) {
			$q[] = "realname='" . addslashes(strip_tags($u['realname'])). "'";
		}

		if (@$u['homePage']) {
			$q[] = "homePage='" . addslashes(strip_tags($u['homePage'])). "'";
		}

		if (@$u['country']) {
			$q[] = "country='" . addslashes(strip_tags($u['country'])). "'";
		}

		$query = "update users_users set " . implode(",", $q). " where " . $this->convert_binary(). " `login`='{$u['login']}'";
		$result = $this->query($query);
		return $result;
	}
}

?>
