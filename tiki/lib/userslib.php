<?php
  // Lib for user administration, groups and permissions
  // This lib uses pear so the constructor requieres
  // a pear DB object

class UsersLib {
  var $db;  // The PEAR db object used to access the database
    
  function UsersLib($db) 
  {
    if(!$db) {
      die("Invalid db object passed to UsersLib constructor");  
    }
    $this->db = $db;  
  }
  
  function sql_error($query, $result) 
  {
    trigger_error("MYSQL error:  ".$result->getMessage()." in query:<br/>".$query."<br/>",E_USER_WARNING);
    die;
  }

  function set_admin_pass($pass) 
  {
    $query = "update users_users set password='$pass' where login='admin'";
    $result=$this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  function assign_object_permission($groupName,$objectId,$objectType,$permName)
  {
    $objectId = md5($objectType.$objectId);
    $query = "replace into users_objectPermissions(groupName,objectId,objectType,permName) values('$groupName','$objectId','$objectType','$permName')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function object_has_permission($user,$objectId,$objectType,$permName)
  {
    $groups = $this->get_user_groups($user);
    $objectId = md5($objectType.$objectId);
    foreach($groups as $groupName) {
      $query = "select permName from users_objectPermissions where groupName='$groupName' and objectId='$objectId' and objectType='$objectType' and permName = '$permName'";
      $result = $this->db->query($query);
      if(DB::isError($result)) $this->sql_error($query,$result);
      if($result->numRows()) return true;
    }
    return false;
  }
  
  function remove_object_permission($groupName,$objectId,$objectType,$permName)
  {
    $objectId = md5($objectType.$objectId);
    $query = "delete from users_objectPermissions where groupName='$groupName' and objectId='$objectId' and objectType='$objectType' and permName='$permName'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function get_object_permissions($objectId,$objectType)
  {
    $objectId = md5($objectType.$objectId);
    $query = "select groupName,permName from users_objectPermissions where objectId='$objectId' and objectType='$objectType'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret;
  }
  
  function object_has_one_permission($objectId,$objectType)
  {
    $objectId = md5($objectType.$objectId);
    $query = "select objectId,objectType from users_objectPermissions where objectId='$objectId' and objectType='$objectType'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return $result->numRows(); 
  }
  

  function user_exists($user) {
    $query = "select login from users_users where login='$user'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return $result->numRows();
  }
  
  function group_exists($group) {
    $query = "select groupName from users_groups where groupName='$group'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return $result->numRows();
  }
  
  function validate_user($user,$pass)
  {
    $query = "select login from users_users where login='$user' and password='$pass'"; 
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    if($result->numRows()) {
    $t = date("U");
    $query = "update users_users set lastLogin='$t' where login='$user'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
      return true; 
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
    $query = "select login, email, lastLogin from users_users $mid order by $sort_mode limit $offset,$maxRecords";
    
    $query_cant = "select count(*) from users_users";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["user"] = $res["login"];
      $user = $aux["user"];
      $aux["email"] = $res["email"];
      $aux["lastLogin"] = $res["lastLogin"];
      $groups = $this->get_user_groups($user);
      $aux["groups"] = $groups;
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function remove_user_from_group($user,$group) 
  {
    $userid = $this->get_user_id($user);
    $query = "delete from users_userGroups where userId=$userid and groupName='$group'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
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
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["groupName"] = $res["groupName"];
      $aux["groupDesc"] = $res["groupDesc"];
      $perms = $this->get_group_permissions($aux["groupName"]);
      $aux["perms"] = $perms;
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function get_user_id($user)
  {
    $id = $this->db->getOne("select userId from users_users where login='$user'");
    if(DB::isError($id)) return false;
    return $id;  
  }
  
  function remove_user($user)
  {
    $query = "delete from users_users where login = '$user'";
    $result =  $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }

  function remove_group($group)
  {
    $query = "delete from users_groups where groupName = '$group'";
    $result =  $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function get_user_groups($user) 
  {
    $userid = $this->get_user_id($user);
    $query = "select groupName from users_userGroups where userId='$userid'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["groupName"];  
    }
    $ret[] = "Anonymous";
    return $ret;
  }

  function get_user_info($user) 
  {
    $query = "select * from users_users where login='$user'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
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
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $aux = Array();
    foreach ($res as $key => $val) {
      $aux[$key] = $val;  
    }
    $groups = $this->get_user_groups($user);
    $res["groups"] = $groups;
    return $res;
  }
  
  function get_permissions($offset = 0,$maxRecords = -1,$sort_mode = 'permName_desc', $find='',$type='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    // Return an array of users indicating name, email, last changed pages, versions, lastLogin 
    if($find) {
      $mid=" where type like '%$type%' and permName like '%".$find."%'";  
    } else {
      $mid=" where type like '%$type%'"; 
    }
    $query = "select permName, permDesc from users_permissions $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from users_permissions";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query, $result);
    $cant = $this->db->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $aux = Array();
      $aux["permName"] = $res["permName"];
      $aux["permDesc"] = $res["permDesc"];
      $ret[] = $aux;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  function get_group_permissions($group)
  {
    $query = "select permName from users_groupPermissions where groupName='$group'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res["permName"];  
    }
    return $ret;
  }
  
  function assign_permission_to_group($perm,$group) 
  {
    $query = "replace into users_groupPermissions(groupName,permName) values('$group','$perm')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
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
    $groups = get_user_groups($user);
    foreach ($groups as $group) {
      if(group_has_permission($group,$perm)) return true;  
    }
    return false;
  }
  
  function group_has_permission($group,$perm) 
  {
    $query = "select groupName,permName from users_groupPermissions where groupName='$group' and permName='$perm'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return $result->numRows();  
  }
  
  function remove_permission_from_group($perm,$group) 
  {
    $query = "delete from users_groupPermissions where permName='$perm' and groupName= '$group'";
    $result =  $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
  
  function get_group_info($group) 
  {
    $query = "select * from users_groups where groupName='$group'";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $perms = $this->get_group_permissions($group);
    $res["perms"] = $perms;
    return $res;
  }
  
  function assign_user_to_group($user,$group) 
  {
    $userid = $this->get_user_id($user);
    $query = "replace into users_userGroups(userId,groupName) values($userid,'$group')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;  
  }
  
  function add_user($user,$pass,$email)
  {
    if($this->user_exists($user)) return false;  
    $query = "insert into users_users(login,password,email) values('$user','$pass','$email')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    $this->assign_user_to_group($user,'Registered');
    return true;
  }
  
  function add_group($group,$desc)
  {
    if($this->group_exists($group)) return false;  
    $query = "insert into users_groups(groupName, groupDesc) values('$group','$desc')";
    $result = $this->db->query($query);
    if(DB::isError($result)) $this->sql_error($query,$result);
    return true;
  }
}  
?>