<?php
  // Lib for user administration, groups and permissions
  // This lib uses pear so the constructor requieres
  // a pear DB object

// some definitions for helping with authentication
define("USER_VALID",          2);
define("SERVER_ERROR",       -1);
define("PASSWORD_INCORRECT", -3);
define("USER_NOT_FOUND",     -5);
define("ACCOUNT_DISABLED",   -6);

class UsersLib extends TikiLib {
 # var $db;  // The PEAR db object used to access the database

  // change this to an email address to receive debug emails from the LDAP code
  var $debug = false;
  var $usergroups_cache;
  var $groupperm_cache;

  function UsersLib($db) 
  {
    $this->db = $db;  
    // Initialize caches
    $this->usergroups_cache=array();
    $this->groupperm_cache=array(array());
  }

  function set_admin_pass($pass) 
  {
    global $feature_clear_passwords;
    $hash = md5($pass);
    if($feature_clear_passwords == 'n') $pass='';
    $query = "update users_users set password='$pass',hash='$hash' where login='admin'"; 
    $result=$this->query($query);
    return true;
  }

  function assign_object_permission($groupName,$objectId,$objectType,$permName)
  {
    $groupName=addslashes($groupName); 
    $objectId = md5($objectType.$objectId);
    
    $query = "replace into users_objectpermissions(groupName,objectId,objectType,permName) values('$groupName','$objectId','$objectType','$permName')";
    $result = $this->query($query);
    return true;
  }
  
  function object_has_permission($user,$objectId,$objectType,$permName)
  {
    $groups = $this->get_user_groups($user);
    $objectId = md5($objectType.$objectId);
    foreach($groups as $groupName) {
      $query = "select permName from users_objectpermissions where groupName='$groupName' and objectId='$objectId' and objectType='$objectType' and permName = '$permName'";
      $result = $this->query($query);
      if($result->numRows()) return true;
    }
    return false;
  }
  
  function remove_object_permission($groupName,$objectId,$objectType,$permName)
  {
    $objectId = md5($objectType.$objectId);
    $query = "delete from users_objectpermissions where groupName='$groupName' and objectId='$objectId' and objectType='$objectType' and permName='$permName'";
    $result = $this->query($query);
    return true;
  }
  
  function get_object_permissions($objectId,$objectType)
  {
    $objectId = md5($objectType.$objectId);
    $query = "select groupName,permName from users_objectpermissions where objectId='$objectId' and objectType='$objectType'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret;
  }
  
  function object_has_one_permission($objectId,$objectType)
  {
    
    $objectId = md5($objectType.$objectId);
    
    $query = "select objectId,objectType from users_objectpermissions where objectId='$objectId' and objectType='$objectType'";
    $result = $this->query($query);
    return $result->numRows(); 
  }
  

  function user_exists($user) {
    $query = "select login from users_users where login='$user'";
    $result = $this->query($query);
    return $result->numRows();
  }
  
  function group_exists($group) {
    $query = "select groupName from users_groups where groupName='$group'";
    $result = $this->query($query);
    return $result->numRows();
  }
  
  function user_logout($user)
  {
    $t = date("U");
    // No need to change lastLogin since it is handled at the validateUser method
    //$query = "update users_users set lastLogin=$t where login='$user'";
    //$result = $this->query($query);
  }
  
  function genPass()
  {
        $vocales="aeiou";
        $consonantes="bcdfghjklmnpqrstvwxyz";
        $r='';
        for($i=0; $i<5; $i++){
                if ($i%2){
                        $r.=$vocales{rand(0,strlen($vocales)-1)};
                }else{
                        $r.=$consonantes{rand(0,strlen($consonantes)-1)};
                }
        }
        return $r;
  }
  
  function generate_challenge()
  {
    $val = md5($this->genPass());
    return $val;
  }
  
  function validate_hash($user,$hash)
  {
    return $this->db->getOne("select count(*) from users_users where binary login = '$user' and hash='$hash'");
  }

  function validate_user($user,$pass,$challenge,$response)
  {
    global $tikilib;

    // these will help us keep tabs of what is going on
    $userTiki = false;
    $userTikiPresent = false;
    $userAuth = false;
    $userAuthPresent = false;

    // see if we are to use PEAR::Auth
    $auth_pear   = ($tikilib->get_preference("auth_method",           "tiki") == "auth");
    $create_tiki = ($tikilib->get_preference("auth_create_user_tiki", "n")    == "y");
    $create_auth = ($tikilib->get_preference("auth_create_user_auth", "n")    == "y");
    $skip_admin  = ($tikilib->get_preference("auth_skip_admin",       "n")    == "y");

    // first attempt a login via the standard Tiki system
    $result = $this->validate_user_tiki($user, $pass, $challenge, $response);
    switch($result)
    {
      case USER_VALID:
        $userTiki        = true;
        $userTikiPresent = true;
        break;
      case PASSWORD_INCORRECT:
        $userTikiPresent = true;
        break;
    }

    if($this->debug != false)
      mail($this->debug, "tiki result", $result);
    // if we aren't using LDAP this will be quick
    if(!$auth_pear ||
       ($auth_pear && $user == "admin" && $skip_admin) )
    {
      // if the user verified ok, log them in
      if($userTiki)
        return $this->update_lastlogin($user);
      // if the user password was incorrect but the account was there, give an error
      elseif($userTikiPresent)
        return false;
      // if the user was not found, give an error
      // this could be for future uses
      else
        return false;
    }

    // next see if we need to check LDAP
    else
    {
      // check the user account
      $result = $this->validate_user_auth($user, $pass);
      if($this->debug != false)
        mail($this->debug,"result", $result);
      switch($result)
      {
        case USER_VALID:
          $userAuth        = true;
          $userAuthPresent = true;
          break;
        case PASSWORD_INCORRECT:
          $userAuthPresent = true;
          break;
      }

      $msg = "userAuth: $userAuth\n"
            ."userAuthPresent: $userAuthPresent\n"
            ."userTiki: $userTiki\n"
            ."UserTikiPresent: $userTikiPresent\n";
      if($this->debug != false)
        mail($this->debug, "status", $msg);

      // start off easy
      // if the user verified in Tiki and Auth, log in
      if($userAuth && $userTiki)
      {
        if($this->debug != false)
          mail($this->debug, "exit 1", "");
        return $this->update_lastlogin($user);
      }
      // if the user wasn't found in either system, just fail
      elseif(!$userTikiPresent && $userAuthPresent)
      {
        if($this->debug != false)
          mail($this->debug, "exit 2", "");
        return false;
      }
      // if the user was logged into Tiki but not found in Auth
      elseif($userTiki && !$userAuthPresent)
      {
        // see if we can create a new account
        if($create_auth)
        {
          // need to make this better! *********************************************************
          $result = $this->create_user_auth($user, $pass);
          if($this->debug != false)
            mail($this->debug, "exit 3", $result);
          // if it worked ok, just log in
          if($result == USER_VALID)
            // before we log in, update the login counter
            return $this->update_lastlogin($user);
          // if the server didn't work, do something!
          elseif($result == SERVER_ERROR)
          {
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
      elseif($userAuth && !$userTikiPresent)
      {
        if($this->debug != false)
          mail($this->debug, "ok", "4");
        // see if we can create a new account
        if($create_tiki)
        {
          // need to make this better! *********************************************************
          $result = $this->create_user_tiki($user, $pass);
          // if it worked ok, just log in
          if($result == USER_VALID)
            // before we log in, update the login counter
            return $this->update_lastlogin($user);
          // if the server didn't work, do something!
          elseif($result == SERVER_ERROR)
          {
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
      elseif($this->debug != false)
        mail($this->debug, "ok", "5");
    }

    // we will never get here
    if($this->debug != false)
      mail($this->debug, "ok", "6");
    return false;
  }

  // validate the user in the PEAR::Auth system
  function validate_user_auth($user,$pass)
  {
    global $tikilib;
    include_once("Auth/Auth.php");

    // just make sure we're supposed to be here
    if($tikilib->get_preference("auth_method", "tiki") != "auth")
      return false;

    // get all of the LDAP options from the database
    $options["host"]       =  $tikilib->get_preference("auth_ldap_host", "localhost");
    $options["port"]       =  $tikilib->get_preference("auth_ldap_port", "389");
    $options["scope"]      =  $tikilib->get_preference("auth_ldap_scope", "sub");
    $options["basedn"]     =  $tikilib->get_preference("auth_ldap_basedn", "");
    $options["userdn"]     =  $tikilib->get_preference("auth_ldap_userdn", "");
    $options["userattr"]   =  $tikilib->get_preference("auth_ldap_userattr", "uid");
    $options["useroc"]     =  $tikilib->get_preference("auth_ldap_useroc", "posixAccount");
    $options["groupdn"]    =  $tikilib->get_preference("auth_ldap_groupdn", "");
    $options["groupattr"]  =  $tikilib->get_preference("auth_ldap_groupattr", "cn");
    $options["groupoc"]    =  $tikilib->get_preference("auth_ldap_groupoc", "groupOfUniqueNames");
    $options["memberattr"] =  $tikilib->get_preference("auth_ldap_memberattr", "uniqueMember");
    $options["memberisdn"] = ($tikilib->get_preference("auth_ldap_memberisdn", "y") == "y");

    // set the Auth options
    $a = new Auth( "LDAP", $options, "", false, $user, $pass );
    // turn off the error message
    $a->setShowLogin( false );
    $a->start();
    $status = "";

    // check if the login correct
    if ($a->getAuth())
      $status = USER_VALID;

    // otherwise use the error status given back
    else
      $status = $a->getStatus();

    if($this->debug != false)
    {
      $msg = "Status: ".$status."\n";
      foreach($options as $key=>$val)
        $msg .= "$key = $val\n";
      mail($this->debug, "testing auth", $msg);
    }

    return $status;
  }

  // validate the user in the Tiki database
  function validate_user_tiki($user,$pass,$challenge,$response)
  {
    // If the user is loggin in the the lastLogin should be the last currentLogin?

    global $feature_challenge;
    $user=addslashes($user);
    $hash=md5($pass);

    // first verify that the user exists
    $query = "select login from users_users where binary login = '$user' and hash='$hash'";
    $result = $this->query($query);
    if(!$result->numRows())
        return USER_NOT_FOUND;

    // next verify the password
    if($feature_challenge=='n' || empty($response)) {
      $query = "select login from users_users where binary login = '$user' and hash='$hash'";
      $result = $this->query($query);
      if($result->numRows()) {
        return USER_VALID;
      }
    } else {
      // Use challenge-reponse method
      // Compare pass against md5(user,challenge,hash)
      $hash = $this->getOne("select hash from users_users where binary login='$user'");

      if(!isset($_SESSION["challenge"])) return false;
      //print("pass: $pass user: $user hash: $hash <br/>");
      //print("challenge: ".$_SESSION["challenge"]." challenge: $challenge<br/>");
      //print("response : $response<br/>");
      if($response == md5($user.$hash.$_SESSION["challenge"])) {
        return USER_VALID;
      }
    }

    return PASSWORD_INCORRECT;
  }

  // update the lastlogin status on this user
  function update_lastlogin($user)
  {
    $t = date("U");
    // Check
    $current = $this->getOne("select currentLogin from users_users where login='$user'");
    if (is_null($current)) {
      // First time
      $current = $t;
    }
    $query = "update users_users set lastLogin=$current where login='$user'";
    $result = $this->query($query);
    // check
    $query = "update users_users set currentLogin=$t where login='$user'";
    $result = $this->query($query);
    return true;
  }

  // create a new user in the Auth directory
  function create_user_auth($user, $pass)
  {
    global $tikilib;
    $options = array();
    $options["host"]       =  $tikilib->get_preference("auth_ldap_host", "localhost");
    $options["port"]       =  $tikilib->get_preference("auth_ldap_port", "389");
    $options["scope"]      =  $tikilib->get_preference("auth_ldap_scope", "sub");
    $options["basedn"]     =  $tikilib->get_preference("auth_ldap_basedn", "");
    $options["userdn"]     =  $tikilib->get_preference("auth_ldap_userdn", "");
    $options["userattr"]   =  $tikilib->get_preference("auth_ldap_userattr", "uid");
    $options["useroc"]     =  $tikilib->get_preference("auth_ldap_useroc", "posixAccount");
    $options["groupdn"]    =  $tikilib->get_preference("auth_ldap_groupdn", "");
    $options["groupattr"]  =  $tikilib->get_preference("auth_ldap_groupattr", "cn");
    $options["groupoc"]    =  $tikilib->get_preference("auth_ldap_groupoc", "groupOfUniqueNames");
    $options["memberattr"] =  $tikilib->get_preference("auth_ldap_memberattr", "uniqueMember");
    $options["memberisdn"] = ($tikilib->get_preference("auth_ldap_memberisdn", "y") == "y");
    $options["adminuser"]  =  $tikilib->get_preference("auth_ldap_adminuser", "");
    $options["adminpass"]  =  $tikilib->get_preference("auth_ldap_adminpass", "");

    // set additional attributes here
    $userattr = array();
    $userattr["email"] = $this->getOne("select `email` from `users_users` where `login`='$user'");

    // set the Auth options
    $a = new Auth( "LDAP", $options );
    
    // check if the login correct
    if( $a->addUser($user, $pass, $userattr) === true )
      $status = USER_VALID;

    // otherwise use the error status given back
    else
      $status = $a->getStatus();

    // if we're in debug mode, send an email
    if($this->debug)
    {
      $msg = "Status: ".$status."\n";
      foreach($options as $key=>$val)
        $msg .= "$key = $val\n";
      if($this->debug != false)
        mail($this->debug, "create_user_auth", $msg);
    }

    return $status;
  }

  function get_users($offset = 0,$maxRecords = -1,$sort_mode = 'login_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    // Return an array of users indicating name, email, last changed pages, versions, lastLogin 
    if($find) {
      $mid=" where login like '%".$find."%'";  
    } else {
      $mid=''; 
    }
    $query = "select * from users_users $mid order by $sort_mode limit $offset,$maxRecords";
    
    $query_cant = "select count(*) from users_users";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["user"] = $res["login"];
      $user = $aux["user"];
      $aux["email"] = $res["email"];
      $aux["lastLogin"] = $res["lastLogin"];
      $groups = $this->get_user_groups($user);
      $aux["groups"] = $groups;
      $aux["currentLogin"]=$res["currentLogin"];
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function group_inclusion($group,$include)
  {
    $query = "replace into tiki_group_inclusion(groupName,includeGroup) values('$group','$include')";
    $result = $this->query($query);
  }
  
  function get_included_groups($group)
  {
    $query = "select includeGroup from tiki_group_inclusion where groupName='$group'";
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res["includeGroup"];
      $ret2 = $this->get_included_groups($res["includeGroup"]);
      $ret = array_merge($ret,$ret2);
    }
    return array_unique($ret);
  }
  
  function remove_user_from_group($user,$group) 
  {
    $userid = $this->get_user_id($user);
    $query = "delete from users_usergroups where userId=$userid and groupName='$group'";
    $result = $this->query($query);
  }

  function get_groups($offset = 0,$maxRecords = -1,$sort_mode = 'groupName_desc', $find='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    // Return an array of users indicating name, email, last changed pages, versions, lastLogin 
    if($find) {
      $mid=" where groupName like '%".$find."%'";  
    } else {
      $mid=''; 
    }
    $query = "select groupName, groupDesc from users_groups $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from users_groups";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["groupName"] = $res["groupName"];
      $aux["groupDesc"] = $res["groupDesc"];
      $perms = $this->get_group_permissions($aux["groupName"]);
      $aux["perms"] = $perms;
      $groups = $this->get_included_groups($aux["groupName"]);
      $aux["included"]=$groups;
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function get_user_id($user)
  {
    $id = $this->getOne("select userId from users_users where login='$user'");
    return $id;  
  }
  
  function remove_user($user)
  {
    $userId = $this->getOne("select userId from users_users where login = '$user'");
       
    $query = "delete from users_users where login = '$user'";
    $result =  $this->query($query);
    $query = "delete from users_usergroups where userId=$userId";
    $result =  $this->query($query);
    
    return true;
  }

  function remove_group($group)
  {
    $query = "delete from users_groups where groupName = '$group'";
    $result =  $this->query($query);
    $query = "delete from tiki_group_inclusion where groupName = '$group'";
    $result =  $this->query($query);
    return true;
  }
  
  function get_user_groups($user) 
  {
    if(!isset($this->usergroups_cache[$user])) {
    $userid = $this->get_user_id($user);
    $query = "select groupName from users_usergroups where userId='$userid'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["groupName"];  
      $included = $this->get_included_groups($res["groupName"]);
      $ret = array_merge($ret,$included);
    }
    $ret[] = "Anonymous";
    $ret=array_unique($ret);
    // cache it
    $this->usergroups_cache[$user]=$ret;
    return $ret;
    } else {
      return $this->usergroups_cache[$user];
    }
  }
  
  function get_group_users($group)
  {
    $query = "select login from users_users uu,users_usergroups ug where uu.userId=ug.userId and groupName='$group'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["login"];  
    }  
    return $ret;
  }

  function get_user_info($user) 
  {
    $query = "select * from users_users where login='$user'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $aux = Array();
    foreach ($res as $key => $val) {
      $aux[$key] = $val;  
    }
    $groups = $this->get_user_groups($user);
    $res["groups"] = $groups;
    return $res;
  }
  
  function change_permission_level($perm,$level)
  {
    $level=addslashes($level);
    $query = "update users_permissions set level='$level' where permName='$perm'";
    $this->query($query);
  }
  
  function assign_level_permissions($group,$level)
  {
    $query = "select permName from users_permissions where level='$level'";
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $this->assign_permission_to_group($res['permName'],$group);
    }
  }
  
  function remove_level_permissions($group,$level)
  {
    $query = "select permName from users_permissions where level='$level'";
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $this->remove_permission_from_group($res['permName'],$group);
    }
  }
  
  function create_dummy_level($level) {
    $query = "replace into users_permissions(permName,permDesc,type,level) values('','','','$level')";
    $this->query($query);
  }
  
  function get_permission_levels()
  {
    $query = "select distinct(level) from users_permissions";
    $result = $this->query($query);
    $ret=Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[]=$res['level'];
    }
    return $ret;
  }
  
  function get_userid_info($user) 
  {
    $query = "select * from users_users where userId='$user'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $aux = Array();
    foreach ($res as $key => $val) {
      $aux[$key] = $val;  
    }
    $groups = $this->get_user_groups($user);
    $res["groups"] = $groups;
    return $res;
  }
  
  function get_permissions($offset = 0,$maxRecords = -1,$sort_mode = 'permName_desc', $find='',$type='',$group='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    // Return an array of users indicating name, email, last changed pages, versions, lastLogin 
    $mid='';
    if($type) {
      $mid = " where type='$type' ";
    } 
    if($find) {
      if($mid) {
      $mid.=" and permName like '%".$find."%'";  
      } else {
      $mid.=" where permName like '%".$find."%'";  
      }
    } 
    
    $query = "select permName,type,level,permDesc from users_permissions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from users_permissions $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["permName"] = $res["permName"];
      $aux["permDesc"] = $res["permDesc"];
      $aux["type"] = $res["type"];
      $aux['level'] = $res["level"];
      if($group) {
        if($this->group_has_permission($group,$aux["permName"])) {
          $aux["hasPerm"]='y';
        } else {
          $aux["hasPerm"]='n';
        }
      }
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function get_group_permissions($group)
  {
    $query = "select permName from users_grouppermissions where groupName='$group'";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["permName"];  
    }
    return $ret;
  }
  
  function assign_permission_to_group($perm,$group) 
  {
    $query = "replace into users_grouppermissions(groupName,permName) values('$group','$perm')";
    $result = $this->query($query);
    return true;  
  }
  
  function get_user_permissions($user) 
  {
    // admin has all the permissions
    $groups = $this->get_user_groups($user);
    $ret = Array();
    foreach ($groups as $group) {
      $perms = $this->get_group_permissions($group);
      foreach ($perms as $perm) {
        $ret[] = $perm;   
      }
    }
    return $ret;
  }
  
  
  function user_has_permission($user,$perm) 
  {
    // admin has all the permissions
    if($user=='admin') return true;
    // Get user_groups ?  
    $groups = $this->get_user_groups($user);
    foreach ($groups as $group) {
      if($this->group_has_permission($group,$perm)) return true;  
    }
    return false;
  }
  
  function group_has_permission($group,$perm) 
  {
    if(!isset($perm,$this->groupperm_cache[$group][$perm])) {
      $query = "select groupName,permName from users_grouppermissions where groupName='$group' and permName='$perm'";
      $result = $this->query($query);
      $this->groupperm_cache[$group][$perm]=$result->numRows();
      return $result->numRows();
    } else {
      return $this->groupperm_cache[$group][$perm];
    }
  }
  
  function remove_permission_from_group($perm,$group) 
  {
    $query = "delete from users_grouppermissions where permName='$perm' and groupName= '$group'";
    $result =  $this->query($query);
    return true;
  }
  
  function get_group_info($group) 
  {
    $query = "select * from users_groups where groupName='$group'";
    $result = $this->query($query);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $perms = $this->get_group_permissions($group);
    $res["perms"] = $perms;
    return $res;
  }
  
  function assign_user_to_group($user,$group) 
  {
    $userid = $this->get_user_id($user);
    $query = "replace into users_usergroups(userId,groupName) values($userid,'$group')";
    $result = $this->query($query);
    return true;  
  }
  
  function confirm_user($user)
  {
    global $feature_clear_passwords;
    
    $provpass = $this->getOne("select provpass from users_users where login='$user'");
    $hash=md5($provpass);
    if($feature_clear_passwords == 'n') {
      $provpass='';
    }
    $query = "update users_users set password='$provpass',hash='$hash' where login='$user'";
    $result = $this->query($query);
  }
  
  function add_user($user,$pass,$email,$provpass='')
  {
    global $pass_due;
    global $feature_clear_passwords;
    $hash = md5($pass);
    if($feature_clear_passwords == 'n') $pass='';
    if($this->user_exists($user)) return false;  
    $now=date("U");
    $new_pass_due=$now+(60*60*24*$pass_due);
    $query = "insert into users_users(login,password,email,provpass,registrationDate,hash,pass_due,created) values('$user','$pass','$email','$provpass',$now,'$hash',$new_pass_due,$now)";
    $result = $this->query($query);
    $this->assign_user_to_group($user,'Registered');
    return true;
  }

  function change_user_email($user,$email)
  {
    $query = "update users_users set email='$email' where login='$user'";
    $result = $this->query($query);
  }
  
  function get_user_password($user) 
  {
    $query = "select password from users_users where login='$user'";
    $pass = $this->getOne($query);
    return $pass;
  }
  
  function get_user_hash($user) 
  {
    $query = "select hash from users_users where binary login='$user'";
    $pass = $this->getOne($query);
    return $pass;
  }
  
  function get_user_by_hash($hash) 
  {
    $query = "select login from users_users where hash='$hash'";
    $pass = $this->getOne($query);
    return $pass;
  }
  
  function is_due($user)
  {
    $due = $this->getOne("select pass_due from users_users where login='$user'");
    if($due<=date("U")) return true;
    return false;
  }
  
  function renew_user_password($user)
  {
    $pass = $this->genPass();
    $hash = md5($pass);
    // Note that tiki-generated passwords are due inmediatley
    $now=date("U");
    $query = "update users_users set password='$pass', hash='$hash',pass_due=$now where login='$user'";
    $result = $this->query($query);
    return $pass;
  }
  
  function change_user_password($user,$pass)
  {
    global $pass_due;
    global $feature_clear_passwords;
    $hash = md5($pass);
    $now=date("U"); 
    $new_pass_due=$now+(60*60*24*$pass_due);
    if($feature_clear_passwords == 'n') {
      $pass='';
    }
    $query = "update users_users set hash='$hash',password='$pass',pass_due=$new_pass_due where login='$user'";
    $result = $this->query($query);
  }
  
  function add_group($group,$desc)
  {
    if($this->group_exists($group)) return false;  
    $query = "insert into users_groups(groupName, groupDesc) values('$group','$desc')";
    $result = $this->query($query);
    return true;
  }
}  
?>
