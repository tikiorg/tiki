<?php
  // Lib for user administration, groups and permissions
  // This lib uses pear so the constructor requieres
  // a pear DB object

class UsersLib extends TikiLib {
#  var $db;  // The PEAR db object used to access the database
    
  function UsersLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
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
  
  function validate_user($user,$pass,$challenge,$response)
  {
    global $feature_challenge;
    $user=addslashes($user);
    $hash=md5($pass);
    // If the user is loggin in the the lastLogin should be the last currentLogin?
    
    if($feature_challenge=='n' || empty($response)) {
      $query = "select login from users_users where binary login = '$user' and hash='$hash'"; 
      $result = $this->query($query);
      if($result->numRows()) {
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
    } else {
      // Use challenge-reponse method
      // Compare pass against md5(user,challenge,hash)
      $hash = $this->getOne("select hash from users_users where binary login='$user'");
      
      if(!isset($_SESSION["challenge"])) return false;
      //print("pass: $pass user: $user hash: $hash <br/>");
      //print("challenge: ".$_SESSION["challenge"]." challenge: $challenge<br/>");
      //print("response : $response<br/>");
      if($response == md5($user.$hash.$_SESSION["challenge"])) {
        $t = date("U");
        // Check
        $current = $this->getOne("select currentLogin from users_users where login='$user'");
        if (is_null($current)) {
	    // First time
	    $current = $t;
	}
	$query = "update users_users set lastLogin=$current where login='$user'";
        $query = "update users_users set lastLogin=$current where login='$user'";
        $result = $this->query($query);
        // check
        $query = "update users_users set currentLogin=$t where login='$user'";
        $result = $this->query($query);
        return true;
      } else {
        return false;      
      }
    }
    return false;
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
    
    $query = "select permName,type, permDesc from users_permissions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from users_permissions $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["permName"] = $res["permName"];
      $aux["permDesc"] = $res["permDesc"];
      $aux["type"] = $res["type"];
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
    $query = "select groupName,permName from users_grouppermissions where groupName='$group' and permName='$perm'";
    $result = $this->query($query);
    return $result->numRows();  
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
