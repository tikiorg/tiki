<?php
class GUI extends Base {

  /*!
  List user processes, user processes should follow one of these conditions:
  1) The process has an instance assigned to the user
  2) The process has a begin activity with a role compatible to the
     user roles
  3) The process has an instance assigned to '*' and the
     roles for the activity match the roles assigned to
     the user
  The method returns the list of processes that match this
  and it also returns the number of instances that are in the
  process matching the conditions.
  */
  function gui_list_user_processes($user,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and ((gp.name like '%".$find."%') or (gp.description like '%".$find."%'))";
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
    
    $query = "select distinct(gp.pId), 
    				 gp.isActive,                    
                     gp.name as procname, 
                     gp.version as version
              from
              	galaxia_processes gp INNER JOIN galaxia_activities ga ON gp.pId=ga.pId
              	INNER JOIN galaxia_activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN galaxia_roles gr ON gr.roleId=gar.roleId
              	INNER JOIN galaxia_user_roles gur ON gur.roleId=gr.roleId
              where gp.isActive='y' and user='$user'
		            $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(distinct(gp.pId)) from
              	galaxia_processes gp INNER JOIN galaxia_activities ga ON gp.pId=ga.pId
              	INNER JOIN galaxia_activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN galaxia_roles gr ON gr.roleId=gar.roleId
              	INNER JOIN galaxia_user_roles gur ON gur.roleId=gr.roleId
              where gp.isActive='y' and gur.user='$user' $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get instances per activity
      $pId=$res['pId'];
      $res['activities']=$this->getOne("select count(*) from galaxia_processes gp INNER JOIN galaxia_activities ga ON gp.pId=ga.pId INNER JOIN galaxia_activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN galaxia_roles gr ON gr.roleId=gar.roleId
              	INNER JOIN galaxia_user_roles gur ON gur.roleId=gr.roleId
              	where gp.pId=$pId and gur.user='$user'");
	  $res['instances']=$this->getOne("select count(*) from galaxia_instances gi INNER JOIN galaxia_instance_activities gia ON gi.instanceId=gia.instanceId INNER JOIN galaxia_activity_roles gar ON gia.activityId=gar.activityId INNER JOIN galaxia_user_roles gur ON gar.roleId=gur.roleId where gi.pId=$pId and ((gia.user='$user') or (gia.user='*' and gur.user='$user'))");              	
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  function gui_list_user_activities($user,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and ((name like '%".$find."%') or (description like '%".$find."%'))";
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
    
    $query = "select distinct(ga.activityId),                     
    				 ga.name,
    				 ga.type,
                     gp.name as procname, 
                     ga.isInteractive,
                     ga.isAutoRouted,
                     ga.activityId,
                     gp.version as version,
                     gp.pId,
                     gp.isActive
              from
              	galaxia_processes gp INNER JOIN galaxia_activities ga ON gp.pId=ga.pId
              	INNER JOIN galaxia_activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN galaxia_roles gr ON gr.roleId=gar.roleId
              	INNER JOIN galaxia_user_roles gur ON gur.roleId=gr.roleId
              where gp.isActive='y' and user='$user'
		            $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(distinct(gp.pId)) from
              	galaxia_processes gp INNER JOIN galaxia_activities ga ON gp.pId=ga.pId
              	INNER JOIN galaxia_activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN galaxia_roles gr ON gr.roleId=gar.roleId
              	INNER JOIN galaxia_user_roles gur ON gur.roleId=gr.roleId
              where gp.isActive='y' and gur.user='$user' $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get instances per activity
	  $res['instances']=$this->getOne("select count(*) from galaxia_instances gi INNER JOIN galaxia_instance_activities gia ON gi.instanceId=gia.instanceId INNER JOIN galaxia_activity_roles gar ON gia.activityId=gar.activityId INNER JOIN galaxia_user_roles gur ON gar.roleId=gur.roleId where gia.activityId=".$res['activityId']." and ((gia.user='$user') or (gia.user='*' and gur.user='$user'))");              	
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  function gui_list_user_instances($user,$offset,$maxRecords,$sort_mode,$find,$where='')
  {
    $sort_mode = str_replace("_"," ",$sort_mode);
    if($find) {
      $mid=" and ((name like '%".$find."%') or (description like '%".$find."%'))";
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
    
    $query = "select distinct(gi.instanceId),                     
    				 gi.started,
    				 gi.owner,
    				 gia.user,
    				 gi.status,
    				 gia.status as actstatus,
    				 ga.name,
    				 ga.type,
                     gp.name as procname, 
                     ga.isInteractive,
                     ga.isAutoRouted,
                     ga.activityId,
                     gp.version as version,
                     gp.pId
              from
				galaxia_instances gi 
				INNER JOIN galaxia_instance_activities gia ON gi.instanceId=gia.instanceId
				INNER JOIN galaxia_activities ga ON gia.activityId = ga.activityId
				INNER JOIN galaxia_activity_roles gar ON gia.activityId=gar.activityId
				INNER JOIN galaxia_user_roles gur ON gur.roleId=gar.roleId
				INNER JOIN galaxia_processes gp ON gp.pId=ga.pId
				where (gia.user='$user' or (gia.user='*' and gur.user='$user')) $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(distinct(gp.pId)) from
								galaxia_instances gi 
				INNER JOIN galaxia_instance_activities gia ON gi.instanceId=gia.instanceId
				INNER JOIN galaxia_activities ga ON gia.activityId = ga.activityId
				INNER JOIN galaxia_activity_roles gar ON gia.activityId=gar.activityId
				INNER JOIN galaxia_user_roles gur ON gur.roleId=gar.roleId
				INNER JOIN galaxia_processes gp ON gp.pId=ga.pId
				where (gia.user='$user' or (gia.user='*' and gur.user='$user')) $mid ";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get instances per activity
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function gui_abort_instance($user,$activityId,$instanceId)
  {
    // Users can only abort instances belonging to them
	if(!$this->getOne("select count(*) from galaxia_instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'")) return false;
	$query = "update galaxia_instances set status='aborted' where instanceId=$instanceId";
	$this->query($query);
	    	
  }
  
  function gui_exception_instance($user,$activityId,$instanceId)
  {
    // Users can only abort instances belonging to them
	if(!$this->getOne("select count(*) from galaxia_instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'")) return false;	
    $query = "update galaxia_instances set status='exception' where instanceId=$instanceId";
	$this->query($query);
  }

  
  function gui_send_instance($user,$activityId,$instanceId)
  {
	if(!
	  ($this->getOne("select count(*) from galaxia_instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'"))
	  ||
	  ($this->getOne("select count(*) 
	                  from galaxia_instance_activities gia
	                  INNER JOIN galaxia_activity_roles gar ON gar.activityId=gia.activityId
	                  INNER JOIN galaxia_user_roles gur ON gar.roleId=gur.roleId
	                  where gia.instanceId=$instanceId and gia.activityId=$activityId and gia.user='*' and gur.user='$user'
	  				 "))
	  )
 	  return false;	
    include_once('lib/Galaxia/src/API/Instance.php');
    $instance = new Instance($this->db);
    $instance->getInstance($instanceId);
    $instance->complete($activityId,true,false);
    unset($instance);  
  }
  
  function gui_release_instance($user,$activityId,$instanceId)
  {
   	if(!$this->getOne("select count(*) from galaxia_instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'")) return false;
	$query = "update galaxia_instance_activities set user='*' where instanceId=$instanceId and activityId=$activityId";
	$this->query($query);    
  }
  
  function gui_grab_instance($user,$activityId,$instanceId)
  {
	// Grab only if roles are ok  
	if(!$this->getOne("select count(*) 
	                  from galaxia_instance_activities gia
	                  INNER JOIN galaxia_activity_roles gar ON gar.activityId=gia.activityId
	                  INNER JOIN galaxia_user_roles gur ON gar.roleId=gur.roleId
	                  where gia.instanceId=$instanceId and gia.activityId=$activityId and gia.user='*' and gur.user='$user'
	  				 "))	return false;
	$query = "update galaxia_instance_activities set user='$user' where instanceId=$instanceId and activityId=$activityId";
	$this->query($query);    
  }
}
?>