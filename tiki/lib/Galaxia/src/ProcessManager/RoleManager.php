<?php

//!! RoleManager
//! A class to maniplate roles.
/*!
  This class is used to add,remove,modify and list
  roles used in the Workflow engine.
  Roles are managed in a per-process level, each
  role belongs to some process.
*/

/*!TODO
  Add a method to check if a role name exists in a process (to be used
  to prevent duplicate names)
*/

class RoleManager extends BaseManager {
    
  /*!
    Constructor takes a PEAR::Db object to be used
    to manipulate roles in the database.
  */
  function RoleManager($db) 
  {
    if(!$db) {
      die("Invalid db object passed to RoleManager constructor");  
    }
    $this->db = $db;  
  }
  
  function get_role_id($pid,$name)
  {
    $name = addslashes($name);
    return ($this->getOne("select roleId from galaxia_roles where name='$name' and pId=$pid"));
  }
  
  /*!
    Gets a role fields are returned as an asociative array
  */
  function get_role($pId, $roleId)
  {
  	$query = "select * from galaxia_roles where pId=$pId and roleId=$roleId";
	$result = $this->query($query);
	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
	return $res;
  }
  
  /*!
    Indicates if a role exists
  */
  function role_name_exists($pid,$name)
  {
  	$name = addslashes($name);
    return ($this->getOne("select count(*) from galaxia_roles where pId=$pid and name='$name'"));
  }
  
  /*!
   Maps a user to a role
  */
  function map_user_to_role($pId,$user,$roleId)
  {
    $query = "replace into galaxia_user_roles(pId,user,roleId) values($pId,'$user',$roleId)";
    $this->query($query);
  }
  
  /*!
   Removes a mapping
  */
  function remove_mapping($user,$roleId)
  { 
    $query = "delete from galaxia_user_roles where user='$user' and roleId=$roleId";
    $this->query($query);
  }
  
  /*!
   List mappings
  */
  function list_mappings($pId,$offset,$maxRecords,$sort_mode,$find)  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where gr.roleId=gur.roleId and gur.pId=$pId and ((name like $findesc) or (user like $findesc) or (description like $findesc))";
    } else {
      $mid=" where gr.roleId=gur.roleId and gur.pId=$pId ";
    }
    $query = "select name,gr.roleId,user from galaxia_roles gr, galaxia_user_roles gur $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from galaxia_roles gr, galaxia_user_roles gur $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  /*!
   Lists roles at a per-process level
  */
  function list_roles($pId,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where pId=$pId and ((name like $findesc) or (description like $findesc))";
    } else {
      $mid=" where pId=$pId ";
    }
    if($where) {
      $mid.= " and ($where) ";
    }
    $query = "select * from galaxia_roles $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from galaxia_roles $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }
  
  
  
  /*! 
  	Removes a role.
  */
  function remove_role($pId, $roleId)
  {
    $query = "delete from galaxia_roles where pId=$pId and roleId=$roleId";
    $this->query($query);
    $query = "delete from galaxia_activity_roles where roleId=$roleId";
    $this->query($query);
    $query = "delete from galaxia_user_roles where roleId=$roleId";
    $this->query($query);
  }
  
  /*!
	Updates or inserts a new role in the database, $vars is an asociative
	array containing the fields to update or to insert as needed.
	$pId is the processId
	$roleId is the roleId  
  */
  function replace_role($pId, $roleId, $vars)
  {
    $TABLE_NAME = 'galaxia_roles';
    $now = date("U");
    $vars['lastModif']=$now;
    $vars['pId']=$pId;
    
    foreach($vars as $key=>$value)
    {
      $vars[$key]=addslashes($value);
    }
  
    if($roleId) {
      // update mode
      $first = true;
      $query ="update $TABLE_NAME set";
      foreach($vars as $key=>$value) {
        if(!$first) $query.= ',';
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= " $key=$value ";
        $first = false;
      }
      $query .= " where pId=$pId and roleId=$roleId ";
      $this->query($query);
    } else {
      $name = $vars['name'];
      if ($this->getOne("select count(*) from galaxia_roles where pId=$pId and name='$name'")) {
        return false;
      }
      unset($vars['roleId']);
      // insert mode
      $first = true;
      $query = "insert into $TABLE_NAME(";
      foreach(array_keys($vars) as $key) {
        if(!$first) $query.= ','; 
        $query.= "$key";
        $first = false;
      } 
      $query .=") values(";
      $first = true;
      foreach(array_values($vars) as $value) {
        if(!$first) $query.= ','; 
        if(!is_numeric($value)) $value="'".$value."'";
        $query.= "$value";
        $first = false;
      } 
      $query .=")";
      $this->query($query);
      $roleId = $this->getOne("select max(roleId) from $TABLE_NAME where pId=$pId and lastModif=$now"); 
    }
    // Get the id
    return $roleId;
  }
    
}


?>