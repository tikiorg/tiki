<?php

//!! InstanceManager
//! A class to maniplate instances
/*!
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
  
  function get_instance_activities($iid)
  {
  	$query = "select ga.type,ga.isInteractive,ga.isAutoRouted,gi.pId,ga.activityId,ga.name,gi.instanceId,gi.status,gia.activityId,gia.user,gi.started,gia.status as actstatus from galaxia_activities ga,galaxia_instances gi,galaxia_instance_activities gia where ga.activityId=gia.activityId and gi.instanceId=gia.instanceId and gi.instanceId=$iid";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Number of active instances
      $ret[] = $res;
	}
	return $ret;
  }

  function get_instance($iid)
  {
  	$query = "select * from galaxia_instances gi where instanceId=$iid";
	$result = $this->query($query);
	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
	$res['workitems']=$this->getOne("select count(*) from galaxia_workitems where instanceId=$iid");
	return $res;
  }

  function get_instance_properties($iid)
  {
  	$prop = unserialize($this->getOne("select properties from galaxia_instances gi where instanceId=$iid"));
	return $prop;
  }
  
  function set_instance_properties($iid,&$prop)
  {
  	$props = addslashes(serialize($prop));
  	$query = "update galaxia_instances set properties='$props' where instanceId=$iid";
  	$this->query($query);
  }
  
  function set_instance_owner($iid,$owner)
  {
    $query = "update galaxia_instances set owner='$owner' where instanceId=$iid";
    $this->query($query);
  }
  
  function set_instance_status($iid,$status)
  {
	$query = "update galaxia_instances set status='$status' where instanceId=$iid";
	$this->query($query); 
  }
  
  function set_instance_destination($iid,$activityId)
  {
    $query = "delete from galaxia_instance_activities where instanceId=$iid";
    $this->query($query);
    $query = "insert into galaxia_instance_activities(instanceId,activityId,user,status)
    values($iid,$activityId,'*','running')";
    $this->query($query);
  }
  
  function set_instance_user($iid,$activityId,$user)
  {
  	$query = "update galaxia_instance_activities set user='$user', status='running' where instanceId=$iid and activityId=$activityId";
	$this->query($query);  
  }
  
  

}    



?>