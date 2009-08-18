<?php
include_once(GALAXIA_LIBRARY.'/src/ProcessManager/BaseManager.php');
//!! InstanceManager
//! A class to maniplate instances
/*!
  This class is used to add,remove,modify and list
  instances.
*/
class InstanceManager extends BaseManager {
  
  /*!
    Constructor takes a PEAR::Db object to be used
    to manipulate roles in the database.
  */
  function InstanceManager($db) 
  {
    if(!$db) {
      die("Invalid db object passed to InstanceManager constructor");  
    }
    $this->db = $db;  
  }
  
  function get_instance_activities($iid, $aid = "")
  {
  	$and = ($aid == "") ? "" : "and ga.activityId = $aid";

    $query  = "SELECT ga.activityId, ga.type, ga.type, ga.isInteractive, ga.isInteractive, ga.isAutoRouted,";
	$query .= "gi.pId,ga.activityId,ga.name, gi.instanceId, gi.status, ga.expirationTime AS exptime,";
	$query .= "gia.activityId,gia.user, gi.started, gia.status AS actstatus, gia.started AS iaStarted,";
	$query .= "gia.ended FROM " . GALAXIA_TABLE_PREFIX . "activities ga, " . GALAXIA_TABLE_PREFIX . "instances gi,";
	$query .= GALAXIA_TABLE_PREFIX . "instance_activities gia WHERE ga.activityId=gia.activityId ";
    $query .= "AND gi.instanceId=gia.instanceId AND gi.instanceId=$iid " . $and . " ORDER BY gia.started";
    $result = $this->query($query);
	
    $ret = Array();

    if ($and == "") {
    	while($res = $result->fetchRow()) {
    		// Number of active instances
    		$res['exptime'] = $this->make_ending_date($res['iaStarted'], $res['exptime']);
    		$ret[] = $res;
    	}
    	return $ret;
    }
    else {
    	$res = $result->fetchRow();
    	$res['exptime'] = $this->make_ending_date($res['iaStarted'], $res['exptime']);
    	return $res;
    }
  }

  function get_instance($iid)
  {
    $query = "select * from ".GALAXIA_TABLE_PREFIX."instances gi where instanceId=$iid";
    $result = $this->query($query);
    $res = $result->fetchRow();
    $res['workitems']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."workitems where instanceId=$iid");
    return $res;
  }

  function get_instance_properties($iid)
  {
    $prop = unserialize($this->getOne("select properties from ".GALAXIA_TABLE_PREFIX."instances gi where instanceId=$iid"));
    return $prop;
  }
  
  function set_instance_properties($iid,&$prop)
  {
    $props = addslashes(serialize($prop));
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set properties='$props' where instanceId=$iid";
    $this->query($query);
  }
  
  function set_instance_name($iid,$name)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set name='$name' where instanceId=$iid";
    $this->query($query);
  }
  
  function set_instance_owner($iid,$owner)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set owner='$owner' where instanceId=$iid";
    $this->query($query);
  }
  
  function set_instance_status($iid,$status)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set status='$status' where instanceId=$iid";
    $this->query($query); 
  }
  
  function set_instance_destination($iid,$activityId)
  {
	//This avoids duplicate keys in instance_activities
    $query = "DELETE FROM " . GALAXIA_TABLE_PREFIX . "instance_activities WHERE instanceId=$iid";
    $this->query($query);

	//Now let's insert the info about the activity we're sending the instance to
    $query  = "INSERT INTO " . GALAXIA_TABLE_PREFIX . "instance_activities (instanceId, activityId, user, status)";
	$query .= " VALUES ($iid, $activityId, '*', 'running')";
    $this->query($query);
  }
  
  function set_instance_user($iid,$activityId,$user)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instance_activities set user='$user', status='running' where instanceId=$iid and activityId=$activityId";
    $this->query($query);  
  }

}    

?>
