<?php
class ProcessMonitor extends Base {

  function monitor_stats()
  {
    $res = Array();
    $res['active_processes']=$this->getOne("select count(*) from galaxia_processes where isActive='y'");
    $res['processes']=$this->getOne("select count(*) from galaxia_processes");
    $result=$this->query("select distinct(pId) from galaxia_instances where status='active'");
    $res['running_processes']=$result->numRows();
    $res['active_instances']=$this->getOne("select count(*) from galaxia_instances where status='active'");
    $res['completed_instances']=$this->getOne("select count(*) from galaxia_instances where status='completed'");
    $res['exception_instances']=$this->getOne("select count(*) from galaxia_instances where status='exception'");
    $res['aborted_instances']=$this->getOne("select count(*) from galaxia_instances where status='aborted'");
    return $res;
  }
  
  function update_instance_status($iid,$status)
  {
  	$query = "update galaxia_instances set status='$status' where instanceId=$iid";
  	$this->query($query);
  }
  
  function update_instance_activity_status($iid,$activityId,$status)
  {
  	$query = "update galaxia_instance_activities set status='$status' where instanceId=$iid and activityId=$activityId";
  	$this->query($query);
  
  }
  
  function remove_instance($iid)
  {
	$query = "delete from galaxia_workitems where instanceId=$iid";
	$this->query($query);
	$query = "delete from galaxia_instance_activities where instanceId=$iid";
	$this->query($query);
	$query = "delete from galaxia_instances where instanceId=$iid";
	$this->query($query);  
  }
  
  function remove_aborted()
  {
	
	$query="select instanceId from galaxia_instances where status='aborted'";
	$result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {	
      $iid = $res['instanceId'];
      $query = "delete from galaxia_instance_activities where instanceId=$iid";
	  $this->query($query);
	  $query = "delete from galaxia_workitems where instanceId=$iid";
	  $this->query($query);  
    }
	$query = "delete from galaxia_instances where status='aborted'";
	$this->query($query);
	
  }

  function remove_all($pId)
  {
	$query="select instanceId from galaxia_instances where pId=$pId";
	$result = $this->query($query);
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {	
      $iid = $res['instanceId'];
      $query = "delete from galaxia_instance_activities where instanceId=$iid";
	  $this->query($query);
	  $query = "delete from galaxia_workitems where instanceId=$iid";
	  $this->query($query);  
    }
	$query = "delete from galaxia_instances where pId=$pId";
	$this->query($query);
	
  }


  
  function monitor_list_processes($offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where ((name like $findesc) or (description like $findesc))";
    } else {
      $mid="";
    }
    if($where) {
      if($mid) {
        $mid.= " and ($where) ";
      } else {
        $mid.= " where ($where) ";
      }
    }
    $query = "select * from galaxia_processes $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from galaxia_processes $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Number of active instances
      $res['active_instances']=$this->getOne("select count(*) from galaxia_instances where status='active' and pId=".$res['pId']);
      // Number of exception instances
      $res['exception_instances']=$this->getOne("select count(*) from galaxia_instances where status='exception' and pId=".$res['pId']);
      // Number of completed instances
      $res['completed_instances']=$this->getOne("select count(*) from galaxia_instances where status='completed' and pId=".$res['pId']);
      // Number of aborted instances
      $res['aborted_instances']=$this->getOne("select count(*) from galaxia_instances where status='aborted' and pId=".$res['pId']);
      $res['all_instances']=$this->getOne("select count(*) from galaxia_instances where pId=".$res['pId']);
      // Number of activities
      $res['activities']=$this->getOne("select count(*) from galaxia_activities where pId=".$res['pId']);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function monitor_list_activities($offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where ((name like $findesc) or (description like $findesc))";
    } else {
      $mid="";
    }
    if($where) {
      if($mid) {
        $mid.= " and ($where) ";
      } else {
        $mid.= " where ($where) ";
      }
    }
    $query = "select * from galaxia_activities $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from galaxia_activities $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Number of active instances
      $aid = $res['activityId'];
      $res['active_instances']=$this->getOne("select count(gi.instanceId) from galaxia_instances gi,galaxia_instance_activities gia where gi.instanceId=gia.instanceId and gia.activitYId=$aid and gi.status='active' and pId=".$res['pId']);
      $res['completed_instances']=$this->getOne("select count(gi.instanceId) from galaxia_instances gi,galaxia_instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gi.status='completed' and pId=".$res['pId']);
      $res['aborted_instances']=$this->getOne("select count(gi.instanceId) from galaxia_instances gi,galaxia_instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gi.status='aborted' and pId=".$res['pId']);
      $res['exception_instances']=$this->getOne("select count(gi.instanceId) from galaxia_instances gi,galaxia_instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gi.status='exception' and pId=".$res['pId']);
	  $res['act_running_instances']=$this->getOne("select count(gi.instanceId) from galaxia_instances gi,galaxia_instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gia.status='running' and pId=".$res['pId']);      
      $res['act_completed_instances']=$this->getOne("select count(gi.instanceId) from galaxia_instances gi,galaxia_instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gia.status='completed' and pId=".$res['pId']);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function monitor_list_instances($offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" where ((properties like $findesc)";
    } else {
      $mid="";
    }
    if($where) {
      if($mid) {
        $mid.= " and ($where) ";
      } else {
        $mid.= " where ($where) ";
      }
    }
    
    $query = "
    		select  gp.pId,
                     ga.isInteractive,
                     gi.owner,
                     gp.name as procname,
                     gp.version,
                     ga.type,
                     ga.activityId,
                     ga.name,
                     gi.instanceId,
                     gi.status,
                     gia.activityId,
                     gia.user,
                     gi.started,
                     gia.status as actstatus 
       		from 
    		   galaxia_instances gi LEFT JOIN galaxia_instance_activities gia ON gi.instanceId=gia.instanceId 
    		   LEFT JOIN galaxia_activities ga ON gia.activityId = ga.activityId 
    		   LEFT JOIN galaxia_processes gp ON gp.pId=gi.pId 
    		$mid order by $sort_mode limit $offset,$maxRecords";   

    $query_cant = "select count(*) from 
    		   galaxia_instances gi LEFT JOIN galaxia_instance_activities gia ON gi.instanceId=gia.instanceId 
    		   LEFT JOIN galaxia_activities ga ON gia.activityId = ga.activityId 
    		   LEFT JOIN galaxia_processes gp ON gp.pId=gi.pId             $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $iid = $res['instanceId'];
      $res['workitems']=$this->getOne("select count(*) from galaxia_workitems where instanceId=$iid");
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  function monitor_list_all_processes($sort_mode)
  {
      
    $query = "select distinct(name),pId from galaxia_processes order by $sort_mode";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret;
  }
  
  function monitor_list_statuses()
  {
    $query = "select distinct(status) from galaxia_instances";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['status'];
    }
    return $ret;
  }
  
  function monitor_list_users()
  {
    $query = "select distinct(user) from galaxia_instance_activities";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['user'];
    }
    return $ret;
  }

  function monitor_list_wi_users()
  {
    $query = "select distinct(user) from galaxia_workitems";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['user'];
    }
    return $ret;
  }

  
  function monitor_list_owners()
  {
    $query = "select distinct(owner) from galaxia_instances";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['owner'];
    }
    return $ret;
  }
  
  
  function monitor_list_activity_types()
  {
    $query = "select distinct(type) from galaxia_activities";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['type'];
    }
    return $ret;  
  }
  
  function monitor_get_workitem($itemId)
  {
    $query = "select gw.orderId,ga.name,ga.type,ga.isInteractive,gp.name as procname,gp.version,gw.itemId,gw.properties,gw.user,started,ended-started as duration from galaxia_workitems gw,galaxia_activities ga,galaxia_processes gp where ga.activityId=gw.activityId and ga.pId=gp.pId and itemId=$itemId";
    $result = $this->query($query);    
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $res['properties']=unserialize($res['properties']);
    return $res;
  }

  // List workitems per instance, remove workitem, update_workitem
  function monitor_list_workitems($offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
	$findesc = $this->qstr('%'.$find.'%');
      $mid=" and ((properties like $findesc) or (name like $findesc))";
    } else {
      $mid="";
    }
    if($where) {
      if($mid) {
        $mid.= " and ($where) ";
      } else {
        $mid.= " and ($where) ";
      }
    }
    $query = "select itemId,ended-started as duration,ga.isInteractive, ga.type,gp.name as procname,gp.version,ga.name as actname, ga.activityId,instanceId,orderId,properties,started,ended,user from galaxia_workitems gw,galaxia_activities ga,galaxia_processes gp where gw.activityId=ga.activityId and ga.pId=gp.pId $mid order by gp.pId,$sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from galaxia_workitems gw,galaxia_activities ga,galaxia_processes gp where gw.activityId=ga.activityId and ga.pId=gp.pId  $mid";
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
  

}
?>