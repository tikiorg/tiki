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
  
  function get_instance_activities($iid,$aid = "")
  {
  	if ($aid == "") {
  		$and = "";
  	}
  	else {
  		$and = "and ga.activityId = $aid";
  	}
    $query  = "select ga.activityId,ga.type,ga.type,ga.isInteractive,ga.isInteractive,ga.isAutoRouted,gi.pId,ga.activityId,ga.name,";
    $query .= "gi.instanceId,gi.status,ga.expirationTime as exptime,gia.activityId,gia.user,";
    $query .= "gi.started,gia.status as actstatus,gia.started as iaStarted,gia.ended from ";
    $query .= GALAXIA_TABLE_PREFIX."activities ga,".GALAXIA_TABLE_PREFIX."instances gi,";
    $query .= GALAXIA_TABLE_PREFIX."instance_activities gia where ga.activityId=gia.activityId ";
    $query .= "and gi.instanceId=gia.instanceId and gi.instanceId=$iid ".$and." order by gia.started";
    $result = $this->query($query);
    $ret = Array();
    if ($and == "") {
    	while($res = $result->fetchRow()) {
    		// Number of active instances
    		$res['exptime'] = $this->make_ending_date($res['iaStarted'],$res['exptime']);
    		$ret[] = $res;
    	}
    	return $ret;
    }
    else {
    	$res = $result->fetchRow();
    	$res['exptime'] = $this->make_ending_date($res['iaStarted'],$res['exptime']);
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
    $query = "delete from ".GALAXIA_TABLE_PREFIX."instance_activities where instanceId=$iid";
    $this->query($query);
    $query = "insert into ".GALAXIA_TABLE_PREFIX."instance_activities(instanceId,activityId,user,status)
    values($iid,$activityId,'*','running')";
    $this->query($query);
  }
  
  function set_instance_user($iid,$activityId,$user)
  {
    $query = "update ".GALAXIA_TABLE_PREFIX."instance_activities set user='$user', status='running' where instanceId=$iid and activityId=$activityId";
    $this->query($query);  
  }

}    

?>
