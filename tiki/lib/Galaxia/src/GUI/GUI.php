<?php
include_once(GALAXIA_LIBRARY.'/src/common/Base.php');
//!! GUI
//! A GUI class for use in typical user interface scripts
/*!
This class provides methods for use in typical user interface scripts
*/
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
      $findesc = $this->qstr('%'.$find.'%');
      $mid=" and ((gp.name like $findesc) or (gp.description like $findesc))";
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
                     gp.normalized_name as normalized_name, 
                     gp.version as version
              from
              	".GALAXIA_TABLE_PREFIX."processes gp INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.pId=ga.pId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.roleId=gar.roleId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.roleId=gr.roleId
              where gp.isActive='y' and user='$user'
		            $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(distinct(gp.pId)) from
              	".GALAXIA_TABLE_PREFIX."processes gp INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.pId=ga.pId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.roleId=gar.roleId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.roleId=gr.roleId
              where gp.isActive='y' and gur.user='$user' $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get instances per activity
      $pId=$res['pId'];
      $res['activities']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."processes gp INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.pId=ga.pId INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.roleId=gar.roleId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.roleId=gr.roleId
              	where gp.pId=$pId and gur.user='$user'");
	  $res['instances']=$this->getOne("select count(distinct(gi.instanceId)) from ".GALAXIA_TABLE_PREFIX."instances gi INNER JOIN ".GALAXIA_TABLE_PREFIX."instance_activities gia ON gi.instanceId=gia.instanceId INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gia.activityId=gar.activityId INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gar.roleId=gur.roleId where gi.pId=$pId and ((gia.user='$user') or (gia.user='*' and gur.user='$user'))");              	
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
      $findesc = $this->qstr('%'.$find.'%');
      $mid=" and ((ga.name like $findesc) or (ga.description like $findesc))";
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
              	".GALAXIA_TABLE_PREFIX."processes gp INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.pId=ga.pId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.roleId=gar.roleId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.roleId=gr.roleId
              where gp.isActive='y' and user='$user'
		            $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(distinct(ga.activityId)) from
              	".GALAXIA_TABLE_PREFIX."processes gp INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gp.pId=ga.pId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.activityId=ga.activityId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."roles gr ON gr.roleId=gar.roleId
              	INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.roleId=gr.roleId
              where gp.isActive='y' and gur.user='$user' $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Get instances per activity
	  $res['instances']=$this->getOne("select count(distinct(gi.instanceId)) from ".GALAXIA_TABLE_PREFIX."instances gi INNER JOIN ".GALAXIA_TABLE_PREFIX."instance_activities gia ON gi.instanceId=gia.instanceId INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gia.activityId=gar.activityId INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gar.roleId=gur.roleId where gia.activityId=".$res['activityId']." and ((gia.user='$user') or (gia.user='*' and gur.user='$user'))");              	
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
      $findesc = $this->qstr('%'.$find.'%');
      $mid=" and ((ga.name like $findesc) or (ga.description like $findesc))";
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
				".GALAXIA_TABLE_PREFIX."instances gi 
				INNER JOIN ".GALAXIA_TABLE_PREFIX."instance_activities gia ON gi.instanceId=gia.instanceId
				INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gia.activityId = ga.activityId
				INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gia.activityId=gar.activityId
				INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.roleId=gar.roleId
				INNER JOIN ".GALAXIA_TABLE_PREFIX."processes gp ON gp.pId=ga.pId
				where (gia.user='$user' or (gia.user='*' and gur.user='$user')) $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(distinct(gi.instanceId)) from
								".GALAXIA_TABLE_PREFIX."instances gi 
				INNER JOIN ".GALAXIA_TABLE_PREFIX."instance_activities gia ON gi.instanceId=gia.instanceId
				INNER JOIN ".GALAXIA_TABLE_PREFIX."activities ga ON gia.activityId = ga.activityId
				INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gia.activityId=gar.activityId
				INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gur.roleId=gar.roleId
				INNER JOIN ".GALAXIA_TABLE_PREFIX."processes gp ON gp.pId=ga.pId
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

// TODO: shouldn't this stop the whole instance, including all activities ?
  function gui_abort_instance($user,$activityId,$instanceId)
  {
    // Users can only abort instances belonging to them
	if(!$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'")) return false;
	$query = "update ".GALAXIA_TABLE_PREFIX."instances set status='aborted' where instanceId=$instanceId";
	$this->query($query);
	    	
  }
  
  function gui_exception_instance($user,$activityId,$instanceId)
  {
    // Users can only abort instances belonging to them
	if(!$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'")) return false;	
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set status='exception' where instanceId=$instanceId";
	$this->query($query);
  }

  function gui_resume_instance($user,$activityId,$instanceId)
  {
    // Users can only resume instances belonging to them
	if(!$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'")) return false;	
    $query = "update ".GALAXIA_TABLE_PREFIX."instances set status='active' where instanceId=$instanceId";
	$this->query($query);
  }

  
  function gui_send_instance($user,$activityId,$instanceId)
  {
	if(!
	  ($this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'"))
	  ||
	  ($this->getOne("select count(*) 
	                  from ".GALAXIA_TABLE_PREFIX."instance_activities gia
	                  INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.activityId=gia.activityId
	                  INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gar.roleId=gur.roleId
	                  where gia.instanceId=$instanceId and gia.activityId=$activityId and gia.user='*' and gur.user='$user'
	  				 "))
	  )
 	  return false;	
    include_once(GALAXIA_LIBRARY.'/src/API/Instance.php');
    $instance = new Instance($this->db);
    $instance->getInstance($instanceId);
    $instance->complete($activityId,true,false);
    unset($instance);  
  }
  
  function gui_release_instance($user,$activityId,$instanceId)
  {
   	if(!$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instance_activities where activityId=$activityId and instanceId=$instanceId and user='$user'")) return false;
	$query = "update ".GALAXIA_TABLE_PREFIX."instance_activities set user='*' where instanceId=$instanceId and activityId=$activityId";
	$this->query($query);    
  }
  
  function gui_grab_instance($user,$activityId,$instanceId)
  {
	// Grab only if roles are ok  
	if(!$this->getOne("select count(*) 
	                  from ".GALAXIA_TABLE_PREFIX."instance_activities gia
	                  INNER JOIN ".GALAXIA_TABLE_PREFIX."activity_roles gar ON gar.activityId=gia.activityId
	                  INNER JOIN ".GALAXIA_TABLE_PREFIX."user_roles gur ON gar.roleId=gur.roleId
	                  where gia.instanceId=$instanceId and gia.activityId=$activityId and gia.user='*' and gur.user='$user'
	  				 "))	return false;
	$query = "update ".GALAXIA_TABLE_PREFIX."instance_activities set user='$user' where instanceId=$instanceId and activityId=$activityId";
	$this->query($query);    
  }
}
?>
