<?php

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

class UsersLib extends TikiLib {
# var $db;  // The PEAR db object used to access the database

    // change this to an email address to receive debug emails from the LDAP code
    var $debug = false;

    var $usergroups_cache;
    var $groupperm_cache;
    var $groupinclude_cache;

    function UsersLib($db) {
	$this->db = $db;

	// Initialize caches
	$this->usergroups_cache = array();
	$this->groupperm_cache = array(array());
	$this->groupinclude_cache = array();
    }

    function set_admin_pass($pass) {
	global $feature_clear_passwords;

	$query = "select `email` from `users_users` where `login` = ?";
	$email = $this->getOne($query, array('admin'));
	$hash = md5("admin" . $pass);

	if ($feature_clear_passwords == 'n')
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

    function object_has_permission($user, $objectId, $objectType, $permName, $group = NULL) {	// the last parameter $group is optional, used when we specify a group but not a user
	if (!empty($group) && empty($user)) {	// only use the provided $group if $group is non-empty and $user is empty
		$groups = array($group);
	} else {	// get groups by looking up the database with $user
	$groups = $this->get_user_groups($user);
	}

	$objectId = md5($objectType . strtolower($objectId));

	foreach ($groups as $groupName) {
	    $query = "select count(*)
		from `users_objectpermissions`
		where `groupName` = ? and `objectId` = ?
		and `objectType` = ? and `permName` = ?";

	    $bindvars = array($groupName, $objectId, $objectType,
		    $permName);
	    $result = $this->getOne($query, $bindvars);

	    if ($result>0)
		return true;
	}

	return false;
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

    function object_has_one_permission($objectId, $objectType) {
	$objectId = md5($objectType . strtolower($objectId));

	$query = "select count(*) from `users_objectpermissions` where `objectId`=? and `objectType`=?";
	$result = $this->getOne($query, array(
		    $objectId,
		    $objectType
		    ));

	return $result;
    }

    function user_exists($user) {
	static $rv = array();

	if (!isset($rv[$user])) {
	    $query = "select count(*) from `users_users` where `login` = ?";

	    $result = $this->getOne($query, array($user));
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
	$t = date("U");
	// No need to change lastLogin since it is handled at the validateUser method
	//$query = "update `users_users` set `lastLogin`=$t where `login`='$user'";
	//$result = $this->query($query);
    }
    
    function user_logout_cas() {
    	global $tikilib;
    	
    	// just make sure we're supposed to be here
		if ($tikilib->get_preference('auth_method', 'tiki') != 'cas') {
		    return false;
		}
		
		$cas_version = $tikilib->get_preference('cas_version', '1.0');
		$cas_hostname = $tikilib->get_preference('cas_hostname');
		$cas_port = $tikilib->get_preference('cas_port');
		$cas_path = $tikilib->get_preference('cas_path');
		
		// import phpCAS lib
		include_once('phpcas/CAS.php');

		phpCAS::setDebug();

		// initialize phpCAS
		phpCAS::client($cas_version, "$cas_hostname", (int) $cas_port, "$cas_path");
		
		// Logout
    	phpCAS::logout();
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
		"select count(*) from `users_users` where " . $this->convert_binary(). " `login` = ? and `hash`=?",
		array($user, $hash)
		);
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
	$userattr["email"] = $this->getOne("select `email` from `users_users`
			where `login`=?", array($user));

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

function get_users($offset = 0, $maxRecords = -1, $sort_mode = 'login_asc', $find = '', $initial = '') {
	
	$now = date('U');
	$mid = '';
	$bindvars = array();
	$mmid = '';
	$mbindvars = array();
	// Return an array of users indicating name, email, last changed pages, versions, lastLogin 
	if ($find) {
	    $mid = " where `login` like ?";
			$mmid = $mid;
	    $bindvars = array('%'.$find.'%');
			$mbindvars = $bindvars;
	}

	if ($initial) {
		$mid = " where `login` like ?";
		$mmid = $mid;
		$bindvars = array($initial.'%');
		$mbindvars = $bindvars;
	}

	$query = "select * from `users_users` $mid order by ".$this->convert_sortmode($sort_mode);

	$query_cant = "select count(*) from `users_users` $mmid";
	$result = $this->query($query, $bindvars, $maxRecords, $offset);
	$cant = $this->getOne($query_cant, $mbindvars);
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $aux = array();

	    $aux["user"] = $res["login"];
	    $aux["userId"] = $res["userId"];
	    $user = $aux["user"];
	    $aux["email"] = $res["email"];
	    $aux["lastLogin"] = $res["lastLogin"];
	    $groups = $this->get_user_groups($user);
	    $aux["groups"] = $groups;
	    $aux["currentLogin"] = $res["currentLogin"];
	    $aux["age"] = $now - $res["registrationDate"];
	    $ret[] = $aux;
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

function get_included_groups($group) {
	$engroup = urlencode($group);
	if (!isset($this->groupinclude_cache[$engroup])) {
		$query = "select `includeGroup`  from `tiki_group_inclusion` where `groupName`=?";
		$result = $this->query($query, array($group));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res["includeGroup"];
			$ret2 = $this->get_included_groups($res["includeGroup"]);
			$ret = array_merge($ret, $ret2);
		}
		$back = array_unique($ret);
		$this->groupinclude_cache[$engroup] = $back;
		return $back; 
	} else {
		return $this->groupinclude_cache[$engroup];
	}
}

    function get_groups($offset = 0, $maxRecords = -1, $sort_mode = 'groupName_desc', $find = '', $initial = '') {

	$mid = "";
	$mmid = "";
	$bindvars = array();
	$mbindvars = array();
	if ($find) {
	    $mid = " where `groupName` like ?";
	    $bindvars[] = "%" . $find . "%";
			$mmid = $mid;
			$mbindvars = $bindvars;
	}

	if ($initial) {
	    $mid = " where `groupName` like ?";
	    $bindvars = array($initial . "%");
			$mmid = $mid;
			$mbindvars = $bindvars;
	}

	$query = "select `groupName` , `groupDesc` from `users_groups` $mid order by ".$this->convert_sortmode($sort_mode);
	$query_cant = "select count(*) from `users_groups` $mmid";
	$result = $this->query($query, $bindvars, $maxRecords, $offset);
	$cant = $this->getOne($query_cant, $mbindvars);
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $aux = array();

	    $aux["groupName"] = $res["groupName"];
	    $aux["groupDesc"] = $res["groupDesc"];
	    $perms = $this->get_group_permissions($aux["groupName"]);
	    $aux["perms"] = $perms;
		$aux["permcant"] = count($perms);
	    $groups = $this->get_included_groups($aux["groupName"]);
	    $aux["included"] = $groups;
	    $ret[] = $aux;
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


    function get_user_id($user) {
	$id = $this->getOne("select `userId` from `users_users` where `login`=?", array($user));

	$id = ($id === NULL) ? -1 : $id;
	return $id;
    }

	function change_login($from,$to) {
		global $cachelib;
		$userId = $this->getOne("select `userId`  from `users_users` where `login` = ?", array($from));
		if ($userId) {
			$this->query("update `users_users` set `login`=? where `userId` = ?", array($to,(int)$userId));
			$this->query("update `tiki_wiki_attachments` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_webmail_messages` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_webmail_contacts` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_users` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_userpoints` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_userfiles` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_watches` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_votings` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_user_tasks` set `user`=? where `user`=?", array($to,$from));
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
			$this->query("update `tiki_minical_topics` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_minical_events` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_minical_topics` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_mailin_accounts` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_minical_topics` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_live_support_requests` set `user`=? where `operator`=?", array($to,$from));
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
			$this->query("update `tiki_file_galleries` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_drawings` set `user`=? where `user`=?", array($to,$from));
			$this->query("update `tiki_download` set `userId`=? where `userId`=?", array((int)$userId,(int)$userId));
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

			$cachelib->invalidate('userslist');
			return true;
		} else {
			return false;
		}
	}

	function get_user_groups($user) {
		$enuser = urlencode($user);
		if (!isset($this->usergroups_cache[$enuser])) {
			$userid = $this->get_user_id($user);
	    $query = "select `groupName` from `users_usergroups` where `userId`=?";
	    $result = $this->query($query, array((int)$userid));
	    $ret = array();
	    while ($res = $result->fetchRow()) {
				$ret[] = $res["groupName"];
				$included = $this->get_included_groups($res["groupName"]);
				$ret = array_merge($ret, $included);
	    }
	    $ret[] = "Anonymous";
	    $ret = array_unique($ret);
	    // cache it
	    $this->usergroups_cache[$enuser] = $ret;
	    return $ret;
		} else {
			return $this->usergroups_cache[$enuser];
		}
	}

	function get_user_default_group($user) {
  	        if (!isset($user)) {
		    return 'Anonymous';
		}  
		$query = "select `default_group` from `users_users` where `login` = ?";
		$result = $this->getOne($query, array($user));
		$ret = '';
		if (!is_null($result)) {
			$ret = $result;
		} else {
			$groups = $this->get_user_groups($user);
			foreach ($groups as $gr) {
				if ($gr != "Anonymous" and $gr != "Registered") {
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

    function get_user_info($user) {
	$query = "select * from `users_users` where `login`=?";
	$result = $this->query($query,array($user));
	$res = $result->fetchRow();
	$groups = $this->get_user_groups($user);
	$res["groups"] = $groups;
	$res["age"] = date('U') - $res['registrationDate'];
	return $res;
    }
    
// Moved from tikilib
    function get_user_details($item, $login) {
		global $user_details;

		if (!isset($user_details[$login])) {
		    $this->load_user_cache($login);
		}

		if ( isset($user_details[$login][$item]) ) {
		    return $user_details[$login][$item];
		} else {
		    return false;
		}
    }


    function set_default_group($user,$group) {
    	// if user is not in group, assign user to group before setting default group
    	$user_groups = $this->get_user_groups($user);
		if (!in_array($group, $user_groups)) {
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

    function get_userid_info($user) {
	$query = "select * from `users_users` where `userId` = ?";
	$result = $this->query($query, array((int)$user));
	$res = $result->fetchRow();
	$res["groups"] = $this->get_user_groups($res['login']);
	if (isset($res['registrationDate']))
		$res["age"] = date('U') - $res['registrationDate'];
	return $res;
    }

	function get_tracker_usergroup($user) {
		$query = "select `default_group` from `users_users` where `login` = ?";
		$result = $this->getOne($query, array($user));
		$ret = '';
		$userTrackerId = $ret;
		if (!is_null($result)) {
			$ret = $this->get_usertrackerid($result);
			if ($ret) {
				$userTrackerId = $ret;
			}
		} 
		if (!$userTrackerId) {
			$groups = $this->get_user_groups($user);
			foreach ($groups as $gr) {
				if ($gr != "Anonymous" and $gr != "Registered") {
					$ret = $this->get_usertrackerid($gr);
					if ($ret) {
						$userTrackerId = $ret;
						break;
					}
				}
			}
		}
		return $ret;
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
		if (!$res) {
			$groups = $this->get_included_groups($group);
			foreach ($groups as $gr) {
				$res = $this->query("select `usersTrackerId`,`usersFieldId` from `users_groups` where `groupName`=?",array($gr));
				if ($res) {
					return $res->fetchRow();
				}
			}
		} else {
			return $res->fetchRow();
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

  function get_permissions($offset = 0, $maxRecords = -1, $sort_mode = 'permName_desc', $find = '', $type = '', $group = '') {
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
	}

	$query = "select `permName`,`type`,`level`,`permDesc` from `users_permissions` $mid order by $sort_mode ";

#	$query_cant = "select count(*) from `users_permissions` $mid";
	$result = $this->query($query, $values, $maxRecords, $offset);
#	$cant = $this->getOne($query_cant, $values);
	$cant = 0;
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $cant++;
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
		    'hasPerm' => $hasPerm,
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
	global $feature_clear_passwords,$cachelib;

	$query = "select `provpass`, `login` from `users_users` where `login`=?";
	$result = $this->query($query, array($user));
	$res = $result->fetchRow();
	// $hash = md5($res["login"] . $res["provpass"] . $res["email"]);
	$hash = md5($res["login"] . $res["provpass"]);
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
			$cachelib->invalidate('userslist');
    }

    function change_user_email($user, $email, $pass) {
	$query = "update `users_users` set `email`=? where " . $this->convert_binary(). " `login`=?";

	$result = $this->query($query, array(
		    $email,
		    $user
		    ));

	// that block stays here for a time (compatibility)
	// lfagundes - only if pass is provided, admin doesn't need it
	// is this still necessary?
	if (!empty($pass)) {
	    $hash = md5($user . $pass);
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
	$query = "select `password`  from `users_users` where " . $this->convert_binary(). " `login`=?";

	$pass = $this->getOne($query, array($user));
	return $pass;
    }

    function get_user_email($user) {
		return $this->getOne("select `email` from `users_users` where " . $this->convert_binary(). " `login`=?", array($user));
    }

    function get_user_hash($user) {
	$query = "select `hash`  from `users_users` where " .
		$this->convert_binary(). " `login` = ?";
	$pass = $this->getOne($query, array($user));
	return $pass;
    }

    function get_user_by_hash($hash) {
	$query = "select `login` from `users_users` where `hash`=?";
	$pass = $this->getOne($query, array($hash));
	return $pass;
    }

    function get_user_by_email($email) {
    $query = "select `login` from `users_users` where `email`=?";
    $pass = $this->getOne($query, array($email));
    return $pass;
    }
    
    function is_due($user) {
    	
    	// if CAS auth is enabled, don't check if password is due since CAS does not use local Tiki passwords
    	if ($this->get_preference('auth_method', 'tiki') == 'cas') {
    		return false;
    	}
    	
	$due = $this->getOne("select `pass_due`  from `users_users` where " . $this->convert_binary(). " `login`=?", array($user));

	if ($due <= date("U"))
	    return true;

	return false;
    }

    function renew_user_password($user) {
	$pass = $this->genPass();
	//$hash = md5($user . $pass . $email);
	$hash = md5($user . $pass);
	// Note that tiki-generated passwords are due inmediatley
	$now = date("U");
	$query = "update `users_users` set `password` = ?, `hash` = ?,
		`pass_due` = ? where ".$this->convert_binary()." `login` = ?";
	$result = $this->query($query, array($pass, $hash, (int)$now, $user));
	return $pass;
    }

    function change_user_password($user, $pass) {
	global $pass_due;

	global $feature_clear_passwords;
	$query = "select `email` from `users_users` where `login` = ?";
	$email = $this->getOne($query, array($user));
	$email=trim($email);
	//$hash = md5($user . $pass . $email);
	$hash = md5($user . $pass);
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
	global $feature_clear_passwords;

	$q = array();
	$bindvars = array();

	if (@$u['password']) {
	    if ($feature_clear_passwords == 's') {
		$q[] = "`password` = ?";
		$bindvars[] = strip_tags($u['password']);
	    }

	    // I don't think there are currently cases where login and email are undefined
	    //$hash = md5($u['login'] . $u['password'] . $u['email']);
	    $hash = md5($u['login'] . $u['password']);
	    $q[] = "`hash` = ?";
	    $bindvars[] = $hash;
	}

	if (@$u['email']) {
	    $q[] = "`email` = ?";
	    $bindvars[] = strip_tags($u['email']);
	}

	if (@$u['realname']) {
	    $q[] = "`realname` = ?";
	    $bindvars[] = strip_tags($u['realname']);
	}

	if (@$u['homePage']) {
	    $q[] = "`homePage` = ?";
	    $bindvars[] = strip_tags($u['homepage']);
	}

	if (@$u['country']) {
	    $q[] = "`country` = ?";
	    $bindvars[] = strip_tags($u['country']);
	}

	$query = "update `users_users` set " . implode(",", $q). " where " .
		$this->convert_binary(). " `login` = ?";
	$bindvars[] = $u['login'];
	$result = $this->query($query, $bindvars);
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

    // Friends methods
    // TODO: if there's already a friendship request from friend to user, accept it
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

}

?>
