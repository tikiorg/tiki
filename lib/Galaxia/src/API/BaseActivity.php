<?php
//!! Abstract class representing activities
//! An abstract class representing activities
/*!
This class represents activities, and must be derived for
each activity type supported in the system. Derived activities extending this
class can be found in the activities subfolder.
This class is observable.
*/
class BaseActivity extends Base {
  var $name;
  var $normalizedName;
  var $description;
  var $isInteractive;
  var $isAutoRouted;
  var $roles=Array();
  var $outbound=Array();
  var $inbound=Array();
  var $pId;
  var $activityId;
  
  function setDb($db)
  {
    $this->db=$db;
  }
  
  function Base($db)
  {
    $this->db=$db;
  }
  
  
  /*!
  Factory method returning an activity of the desired type
  loading the information from the database.
  */
  function getActivity($activityId) 
  {
    $query = "select * from galaxia_activities where activityId=$activityId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    switch($res['type']) {
      case 'start':
    	$act = new Start($this->db);  
      break;
      case 'end':
      	$act = new End($this->db);
      break;
      case 'join':
      	$act = new Join($this->db);
      break;
      case 'split':
      	$act = new Split($this->db);
      break;
      case 'standalone':
      	$act = new Standalone($this->db);
      break;
      case 'switch':
        $act = new SwitchActivity($this->db);
      break;
      case 'activity':
      	$act = new Activity($this->db);
      break;
      default:
      	trigger_error('Unknown activity type:'.$res['type'],E_USER_WARNING);
    }
    
    $act->setName($res['name']);
    $act->setProcessId($res['pId']);
    $act->setNormalizedName($res['normalized_name']);
    $act->setDescription($res['description']);
    $act->setIsInteractive($res['isInteractive']);
    $act->setIsAutoRouted($res['isAutoRouted']);
    $act->setActivityId($res['activityId']);
    
    //Now get forward transitions 
    
    //Now get backward transitions
    
    //Now get roles
    $query = "select roleId from galaxia_activity_roles where activityId=".$res['activityId'];
    $result=$this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $this->roles[] = $res['roleId'];
    }
    $act->setRoles($this->roles);
    return $act;
  }
  
  function getUserRoles($user)
  {
    $query = "select roleId from galaxia_user_roles where user='$user'";
    $result=$this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['roleId'];
    }
    return $ret;
  }
  
  function getActivityRoleNames()
  {
    $aid = $this->activityId;
    $query = "select gr.roleId,name from galaxia_activity_roles gar, galaxia_roles gr where gar.roleId=gr.roleId and gar.activityId=$aid";
    $result=$this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret;
  }
  
  function getNormalizedName() 
  {
  	return $this->normalizedName;
  }
  
  function setNormalizedName($name)
  {
    $this->normalizedName=$name;
  }
  
  function setName($name)
  {
    $this->name=$name;
  }
  
  function getName()
  {
    return $this->name;
  }
  
  function setDescription($desc)
  {
    $this->description=$desc;
  }
  
  function getDescription()
  {
    return $this->description;
  }
  
  function setIsInteractive($is)
  {
    $this->isInteractive=$is;
  }
  
  function isInteractive()
  {
    return $this->isInteractive;
  }
  
  function setIsAutoRouted($is)
  {
    $this->isAutoRouted = $is;
  }
  
  function isAutoRouted()
  {
    return $this->isAutoRouted;
  }

  function setProcessId($pid)
  {
  	$this->pId=$pid;
  }
  
  function getProcessId()
  {
    return $this->pId;
  }

  function getActivityId()
  {
    return $this->activityId;
  }  
  
  function setActivityId($id)
  {
    $this->activityId=$id;
  }
  
  function getRoles()
  {
    return $this->roles;
  }
  
  function setRoles($roles)
  {
    $this->roles = $roles;
  }
  
}
?>