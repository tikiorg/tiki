<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

class UsersLibAdmin extends UsersLib {

        function add_group($group, $desc, $home, $utracker=0, $gtracker=0) {
                global $cachelib;
                if ($this->group_exists($group))
                        return false;
                $query = "insert into `users_groups`(`groupName`, `groupDesc`, `groupHome`,`usersTrackerId`,`groupTrackerId`) values(?,?,?,?,?)";
                $result = $this->query($query, array($group, $desc, $home, (int)$utracker, (int)$gtracker) );
                $cachelib->invalidate('grouplist');
                return true;
        }

    function remove_group($group) {
        global $cachelib;
        $query = "delete from `users_groups` where `groupName` = ?";
        $result = $this->query($query, array($group));
        $query = "delete from `tiki_group_inclusion` where `groupName` = ? or `includeGroup` = ?";
        $result = $this->query($query, array($group, $group));
        $query = "delete from `users_grouppermissions` where `groupName` = ?";
        $result = $this->query($query, array($group));
        $query = "delete from `users_usergroups` where `groupName` = ?";
        $result = $this->query($query, array($group));
        $cachelib->invalidate('grouplist');
        return true;
    }

        function change_group($olgroup,$group,$desc,$home,$utracker=0,$gtracker=0,$ufield=0,$gfield=0) {
                global $cachelib;
                if (!$this->group_exists($olgroup))
                        return $this->add_group($group, $desc, $home,$utracker,$gtracker);
                $query = "update `users_groups` set `groupName`=?, `groupDesc`=?, `groupHome`=?, ";
                $query.= " `usersTrackerId`=?, `groupTrackerId`=?, `usersFieldId`=?, `groupFieldId`=? where `groupName`=?";
                $result = $this->query($query, array($group, $desc, $home, (int)$utracker, (int)$gtracker, (int)$ufield, (int)$gfield, $olgroup));
                $query = "update `users_usergroups` set `groupName`=? where `groupName`=?";
                $result = $this->query($query, array($group, $olgroup));
                $query = "update `users_grouppermissions` set `groupName`=? where `groupName`=?";
                $result = $this->query($query, array($group, $olgroup));
                $query = "update `users_objectpermissions` set `groupName`=? where `groupName`=?";
                $result = $this->query($query, array($group, $olgroup));
                $query = "update `tiki_group_inclusion` set `groupName`=? where `groupName`=?";
                $result = $this->query($query, array($group, $olgroup));
                $query = "update `tiki_newsreader_marks` set `groupName`=? where `groupName`=?";
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

    function add_user($user, $pass, $email, $provpass = '') {
        global $pass_due, $tikilib, $cachelib;
        global $feature_clear_passwords;
        // Generate a unique hash; this is also done below in set_user_fields()
        //$hash = md5($user . $pass . $email);
        $hash = md5($user . $pass);

        if ($feature_clear_passwords == 'n')
            $pass = '';

        if ($this->user_exists($user))
            return false;

        $now = date("U");
        $new_pass_due = $now + (60 * 60 * 24 * $pass_due);
        $query = "insert into
            `users_users`(`login`, `password`, `email`, `provpass`,
                    `registrationDate`, `hash`, `pass_due`, `created`)
            values(?,?,?,?,?,?,?,?)";
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

        if( $tikilib->get_preference("eponymousGroups", 'n') == 'y' )
        {
            // Create a group just for this user, for permissions
            // assignment.
            $this->add_group($user, "Personal group for $user.", '',0,0);

            $this->assign_user_to_group($user, $user);
        }

                        $cachelib->invalidate('userslist');
        return true;
    }


    function remove_user($user) {
                global $cachelib;
        $userId = $this->getOne("select `userId`  from `users_users` where `login` = ?", array($user));

        $query = "delete from `users_users` where `login` = ?";
        $result = $this->query($query, array( $user ) );
        $query = "delete from `users_usergroups` where `userId`=?";
        $result = $this->query($query, array( $userId ) );

        $cachelib->invalidate('userslist');
        return true;
    }

    function remove_user_from_group($user, $group) {
        $userid = $this->get_user_id($user);

        $query = "delete from `users_usergroups` where `userId` = ? and
                `groupName` = ?";
        $result = $this->query($query, array($userid, $group));
    }



    function validate_user(&$user, $pass, $challenge, $response) {
        global $tikilib, $sender_email;

        // these will help us keep tabs of what is going on
        $userTiki = false;
        $userTikiPresent = false;
        $userAuth = false;
        $userAuthPresent = false;

        // see if we are to use PAM
        $auth_pam = ($tikilib->get_preference("auth_method", "tiki") == "pam");
        $pam_create_tiki = ($tikilib->get_preference("pam_create_user_tiki", "n") == "y");
        $pam_skip_admin = ($tikilib->get_preference("pam_skip_admin", "n") == "y");

        // see if we are to use PEAR::Auth
        $auth_pear = ($tikilib->get_preference("auth_method", "tiki") == "auth");
        $create_tiki = ($tikilib->get_preference("auth_create_user_tiki", "n") == "y");
        $create_auth = ($tikilib->get_preference("auth_create_user_auth", "n") == "y");
        $skip_admin = ($tikilib->get_preference("auth_skip_admin", "n") == "y");

        // see if we are to use CAS
        $auth_cas = ($tikilib->get_preference('auth_method', 'tiki') == 'cas');
        $cas_create_tiki = ($tikilib->get_preference('cas_create_user_tiki', 'n') == 'y');
        $cas_skip_admin = ($tikilib->get_preference('cas_skip_admin', 'n') == 'y');

        // first attempt a login via the standard Tiki system
        if (!$auth_cas || $user == 'admin') {
                $result = $this->validate_user_tiki($user, $pass, $challenge, $response);
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
        if ((!$auth_pear && !$auth_pam && !$auth_cas) || ((($auth_pear && $skip_admin) || ($auth_pam && $pam_skip_admin) || ($auth_cas && $cas_skip_admin)) && $user == "admin")) {
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
                        return $this->update_lastlogin($user);
            }
            // if the user wasn't found in either system, just fail
            elseif (!$userTikiPresent && !$userPAM) {
                        return false;
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
            // if the user was logged into PAM and found in Tiki (no password in Tiki user table necessary)
            elseif ($userPAM && $userTikiPresent)
                        return $this->update_lastlogin($user);
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
                        return $this->update_lastlogin($user);
            }
            // if the user wasn't authenticated through CAS, just fail
            elseif (!$userCAS) {
                        return false;
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
            // if the user was authenticated by CAS and found in Tiki (no password in Tiki user table necessary)
            elseif ($userCAS && $userTikiPresent)
                        return $this->update_lastlogin($user);
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
                return $this->update_lastlogin($user);
        }

        // we will never get here
        return false;
    }



  // validate the user through PAM
    function validate_user_pam($user, $pass) {
        global $tikilib;

        // just make sure we're supposed to be here
        if ($tikilib->get_preference("auth_method", "tiki") != "pam")
            return false;

        // get all of the PAM options from the database
        $pam_service = $tikilib->get_preference("pam_service", "tikiwiki");

// Read page AuthPAM at tw.o, it says about a php module required.
// maybe and if extension line could be added here... module requires $error
// as reference.
        if (pam_auth($user, $pass, &$error)) {
                return USER_VALID;
        } else {
        // Uncomment the following to see errors on that
        // error_log("TIKI ERROR PAM:  $error User: $user Pass: $pass");
                return PASSWORD_INCORRECT;
        }
    }

        // validate the user through CAS
        function validate_user_cas(&$user) {
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
                $auth_ext_xml_enabled = $tikilib->get_preference('auth_ext_xml_enabled', 'n');
                $auth_ext_xml_cas_proxy = $tikilib->get_preference('auth_ext_xml_cas_proxy', 'n');
                if ($auth_ext_xml_enabled == 'y' && $auth_ext_xml_cas_proxy == 'y') {
                	phpCAS::proxy($cas_version, "$cas_hostname", (int) $cas_port, "$cas_path");
                } else {
                phpCAS::client($cas_version, "$cas_hostname", (int) $cas_port, "$cas_path");
                }

                // check CAS authentication
                phpCAS::authenticateIfNeeded();

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

        // check if the login correct
        $a->login();
        switch ($a->getStatus()) {
                case AUTH_LOGIN_OK:
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
    function validate_user_tiki($user, $pass, $challenge, $response) {
        global $feature_challenge;

        // first verify that the user exists
        $query = "select `email` from `users_users` where " . $this->convert_binary(). " `login` = ?";
        $result = $this->query($query, array($user) );

        if (!$result->numRows())
        {
            return USER_NOT_FOUND;
        }


        $res = $result->fetchRow();
        $hash=md5($user.$pass.trim($res['email']));
        $hash2 = md5($user.$pass);
        $hash3 = md5($pass);

        // next verify the password with 2 hashes methods, the old one (pass?)) and the new one (login.pass;email)
        if ($feature_challenge == 'n' || empty($response)) {
            $query
                = "select `login` from `users_users` where " . $this->convert_binary(). " `login` = ? and (`hash`=? or `hash`=? or `hash`=?)";

            $result = $this->query($query, array(
                        $user,
                        $hash,
                        $hash2,
                        $hash3
                        ));

            if ($result->numRows()) {
//              $t = date("U");
//
                // Check
//              $current = $this->getOne("select `currentLogin` from `users_users` where `login`=?", array($user));
//
//              if (is_null($current)) {
//                  // First time
//                  $current = $t;
//              }

//              $query = "update `users_users` set `lastLogin`=? where `login`=?";
//              $result = $this->query($query, array(
//                          (int)$current,
//                          $user
//                          ));
                // check
//              $query = "update `users_users` set `currentLogin`=? where `login`=?";
//              $result = $this->query($query, array(
//                          (int)$t,
//                          $user
//                          ));

                return true;
            }
        } else {
            // Use challenge-reponse method
            // Compare pass against md5(user,challenge,hash)
            $hash = $this->getOne("select `hash`  from `users_users` where " . $this->convert_binary(). " `login`=?",
                    array($user) );

            if (!isset($_SESSION["challenge"]))
                return false;

            //print("pass: $pass user: $user hash: $hash <br />");
            //print("challenge: ".$_SESSION["challenge"]." challenge: $challenge<br />");
            //print("response : $response<br />");
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

	
	function validate_user_external_xml($user) {
		require_once('xml/xmlparserlib.php');
		
		global $auth_ext_xml_url;
		if ($tikilib->get_preference('auth_method', 'tiki') == 'cas' && $tikilib->get_preference('auth_ext_xml_', 'tiki') == 'cas') {
			include_once('CAS/CAS.php');
			$service = $auth_ext_xml_url;
			phpCAS::serviceWeb($service,$err_code,$xmloutput);
		} else {
			$handle = fopen($auth_ext_xml_url, "rb");
			$xmloutput = '';
			while (!feof($handle)) {
				$xmloutput .= fread($handle, 8192);
			}
			fclose($handle);
		}
		
		$parser = new XMLParser('$xmloutput', 'raw', 1);
		$tree = $parser->getTree();

		global $auth_ext_xml_group_management, $auth_ext_xml_login_isvalid, $auth_ext_xml_group_management_checked;
		$auth_ext_xml_login_isvalid = false;
		$auth_ext_xml_group_management = false;
		$auth_ext_xml_group_management_checked = false;
		
		function walk($value, $key, $data = null) {
			global $auth_ext_xml_login_isvalid, $auth_ext_xml_group_management, $auth_ext_xml_group_management_checked;
			if (!$auth_ext_xml_login_isvalid && is_array($value)) {
				global $auth_ext_xml_login_element, $auth_ext_xml_login_element_value, $auth_ext_xml_login_attribute, $auth_ext_xml_login_attribute_value;
				global $auth_ext_xml_group_element, $auth_ext_xml_group_element_value, $auth_ext_xml_group_attribute, $auth_ext_xml_group_attribute_value;
				if (array_key_exists($auth_ext_xml_login_element, $value)) {
					foreach ($value[$auth_ext_xml_login_element] as $node) {
						if (empty($auth_ext_xml_login_attribute) || empty($auth_ext_xml_login_attribute_value) ||
							(isset($node['ATTRIBUTES'][$auth_ext_xml_login_attribute]) && $node['ATTRIBUTES'][$auth_ext_xml_login_attribute] == $auth_ext_xml_login_attribute_value)) {
							if (empty($auth_ext_xml_login_element) || empty($auth_ext_xml_login_element_value) ||
								(isset($node['VALUE']) && $node['VALUE'] == $auth_ext_xml_login_element_value)) {
								$auth_ext_xml_login_isvalid = true;
								// check to see if we have found whether we should manage group
								// if so, break out of foreach
								if ($auth_ext_xml_group_management_checked == true || $auth_ext_xml_manage_admin != 'y') {
									break 1;
								}
							}
						}
						if ($auth_ext_xml_group_management_checked != true || $auth_ext_xml_manage_admin == 'y') {
							if (empty($auth_ext_xml_group_attribute) || empty($auth_ext_xml_group_attribute_value) ||
								(isset($node['ATTRIBUTES'][$auth_ext_xml_group_attribute]) && $node['ATTRIBUTES'][$auth_ext_xml_group_attribute] == $auth_ext_xml_group_attribute_value)) {
								if (empty($auth_ext_xml_group_element) || empty($auth_ext_xml_group_element_value) ||
									(isset($node['VALUE']) && $node['VALUE'] == $auth_ext_xml_group_element_value)) {
									$auth_ext_xml_group_management = true;
									// check to see if we have found whether user has access; if so, break out of foreach
									if ($auth_ext_xml_login_isvalid == true) {
										break 1;
									}
								}
							}
						}
					}
				}
			}
		}
		// apply the above function walk() to each node of the array
		array_walk_recursive($tree, "walk");
		
		if ($auth_ext_xml_group_management == 'y') {
			// add user to or remove user from group according to permissions from the XML
		}
		
		global $user, $auth_ext_xml_delete_user_tiki;
		if (!$auth_ext_xml_login_isvalid && $auth_ext_xml_delete_user_tiki == 'y' && $this->user_exists($user)) {
			$this->remove_user($user);
		}
		
		return $auth_ext_xml_login_isvalid;
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

    function remove_permission_from_group($perm, $group) {
    global $cachelib;

    $cachelib->invalidate("allperms");

        $query = "delete from `users_grouppermissions` where `permName` = ?
                and `groupName` = ?";
        $result = $this->query($query, array($perm, $group));
        return true;
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


} // End Class declaration

if (!function_exists('array_walk_recursive')) {
	function array_walk_recursive(&$array, $function, $data=NULL) {
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$function($value, $key, $data);
				array_walk_recursive($value, $function, $data);
			} else {
				$function($value, $key, $data);
			}
		$array[$key] = $value;
		}
	}
}

// create a global instance.
global $userslibadmin;
$userslibadmin = new UsersLibAdmin($dbTiki);

?>
