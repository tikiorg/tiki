<?php
//!! 
//! 
/*!

*/
class Instance extends Base {
  var $properties = Array();
  var $owner = '';
  var $status = '';
  var $started;
  var $nextActivity;
  var $nextUser;
  var $ended;
  /// Array of asocs(activityId,status,started,user)
  var $activities = Array();
  var $pId;
  var $instanceId = 0;
  /// An array of workitem ids
  var $workitems = Array(); 
  
  function Instance($db)
  {
    $this->db = $db;
  }
  
  /*!
  Factory method used to return an instance
  from the database.
  */
  function getInstance($instanceId)
  {
    // Get the instance data
    $query = "select * from galaxia_instances where instanceId=$instanceId";
    $result = $this->query($query);
    if(!$result->numRows()) return false;
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);

	//Populate 
	$this->properties = unserialize($res['properties']);
	$this->status = $res['status'];
	$this->pId = $res['pId'];
	$this->instanceId = $res['instanceId'];
	$this->owner = $res['owner'];
	$this->started = $res['started'];
	$this->ended = $res['ended'];
	$this->nextActivity = $res['nextActivity'];
	$this->nextUser = $res['nextUser'];
    // Get the activities where the instance is (ids only is ok)
    $query = "select * from galaxia_instance_activities where  instanceId=$instanceId";
	$result = $this->query($query);    
	while($res = $result->fetchRow(DB_FETCHMODE_ASSOC))
	{
	  $this->activities[]=$res;
	}    
  }
  
  function setNextActivity($actname)
  {
    $pId = $this->pId;
    $aid = $this->getOne("select activityId from galaxia_activities where pId=$pId and name='$actname'");
      
    if(!$this->getOne("select count(*) from galaxia_activities where activityId=$aid and pId=".$this->pId)) {
    	trigger_error(tra('Fatal error: setting next activity to an unexisting activity'),E_USER_WARNING);
    }
    $this->nextActivity=$aid;
    $query = "update galaxia_instances set nextActivity=$aid where instanceId=".$this->instanceId;
    $this->query($query);
  }

  function setNextUser($user)
  {
    $pId = $this->pId;
	$user = addslashes($user);   
    $this->nextUser=$user;
    $query = "update galaxia_instances set nextUser='$user' where instanceId=".$this->instanceId;
    $this->query($query);
  }

  
  function setNextActivityByName($name)
  {
    $this->nextActivity=$this->getOne("select activityId from galaxia_activities where name='$name' and pId=".$this->pId);
  }
  

    
  /*!
   Creates a new instance for a start activity
  */
  function createNewInstance($activityId,$user)
  {
  	// Creates a new instance setting up started,ended,user
  	// and status
  	$pid = $this->getOne("select pId from galaxia_activities where activityId=$activityId");
  	$this->status = 'active';
  	$this->nextActivity = 0;
  	$this->setNextUser('');
  	$this->pId = $pid;
  	$now = date("U");
  	$this->started=$now;
  	$this->owner = $user;
  	$props=addslashes(serialize($this->properties));
	$query = "insert into galaxia_instances(started,ended,status,pId,owner,properties)
	values($now,0,'active',$pid,'$user','$props')";
  	$this->query($query);
  	$this->instanceId = $this->getOne("select max(instanceId) from galaxia_instances where started=$now and owner='$user'");
  	$iid=$this->instanceId;
  	
  	// Now update the properties!
    $props = addslashes(serialize($this->properties));
    $query = "update galaxia_instances set properties='$props' where instanceId=$iid";
    $this->query($query);

  	
  	// Then add in galaxia_instance_activities an entry for the
  	// activity the user and status running and started now
  	$query = "insert into galaxia_instance_activities(instanceId,activityId,user,started,status)
  	values($iid,$activityId,'$user',$now,'running')";
  	$this->query($query);
  	
  }
  
  
  
  
  /*! sets a property in this instance */
  function set($name,$value)
  {
    $this->properties[$name] = $value;
    $props = addslashes(serialize($this->properties));
    $query = "update galaxia_instances set properties='$props' where instanceId=".$this->instanceId;
    $this->query($query);
    //Save to the database
  }
  
  /*! gets a property in this instance */
  function get($name)
  {
    if(isset($this->properties[$name])) {
      return $this->properties[$name];
    } else {
      return false;
    }
  }
  
  /*! gets activities */
  function getActivities()
  {
    return $this->activities;
  }
  
  /*! gets status */
  function getStatus()
  {
    return $this->status;
  }
  
  /*! set status */
  function setStatus($status)
  {
    $this->status = $status; 
    // and update the database
    $query = "update galaxia_instances set status='$status' where instanceId=".$this->instanceId;
    $this->query($query);  
  }
  
  function getInstanceId()
  {
    return $this->instanceId;
  }
  
  /*! get process */
  function getProcessId()
  {
  	return $this->pId;
  }
  
  /*! get the user */
  function getOwner()
  {
    return $this->owner;
  }
  
  /*! set user */
  function setOwner($user)
  {
    $this->owner = $user;
    // save database
    $query = "update galaxia_instances set owner='$owner' where instanceId=".$this->instanceId;
    $this->query($query);  
  }
  
  function setActivityUser($activityId,$theuser)
  {
    if(empty($theuser)) $theuser='*';
	for($i=0;$i<count($this->activities);$i++) {
	  if($this->activities[$i]['activityId']==$activityId) {
	    $this->activities[$i]['user']=$theuser;
	    $query = "update galaxia_instance_activities set user='$theuser' where activityId=$activityId and instanceId=".$this->instanceId;

	    $this->query($query);
	  }
	}  
  }
  
  function isCandidate($activityId,$user)
  {
	for($i=0;$i<count($this->activities);$i++) {
	  if($this->activities[$i]['activityId']==$activityId) {
	  	$cans = unserialize($this->activities[$i]['candidates']);
	    return in_array($user,$cans);
	  }
	}  
	return false;  
  }
  
  function getActivityUser($activityId)
  {
	for($i=0;$i<count($this->activities);$i++) {
	  if($this->activities[$i]['activityId']==$activityId) {
	    return $this->activities[$i]['user'];
	  }
	}  
	return false;
  }
  
  function setActivityStatus($activityId,$status)
  {
	for($i=0;$i<count($this->activities);$i++) {
	  if($this->activities[$i]['activityId']==$activityId) {
	    $this->activities[$i]['status']=$status;
	    $query = "update galaxia_instance_activities set status='$status' where activityId=$activityId and instanceId=".$this->instanceId;
	    $this->query($query);
	  }
	}  
  }
  
  function getActivityStatus($activityId)
  {
	for($i=0;$i<count($this->activities);$i++) {
	  if($this->activities[$i]['activityId']==$activityId) {
	    return $this->activities[$i]['status'];
	  }
	}  
	return false;
  }
  
  function setActivityStarted($activityId)
  {
  	$now = date("U");
	for($i=0;$i<count($this->activities);$i++) {
	  if($this->activities[$i]['activityId']==$activityId) {
	    $this->activities[$i]['started']=$now;
	    $query = "update galaxia_instance_activities set started=$now where activityId=$activityId and instanceId=".$this->instanceId;
	    $this->query($query);
	  }
	}  
  }
  
  function getActivityStarted($activityId)
  {
	for($i=0;$i<count($this->activities);$i++) {
	  if($this->activities[$i]['activityId']==$activityId) {
	    return $this->activities[$i]['started'];
	  }
	}  
	return false;
  }
  
  function _get_instance_activity($activityId)
  {
	for($i=0;$i<count($this->activities);$i++) {
	  if($this->activities[$i]['activityId']==$activityId) {
	    return $this->activities[$i];
	  }
	}  
	return false;
  }


  
  function setStarted($time)
  {
    $this->started=$time;
    $query = "update galaxia_instances set started=$time where instanceId=".$this->instanceId;
    $this->query($query);    
  }
  
  function getStarted()
  {
    return $this->started;
  }
  
  function setEnded($time)
  {
  	$this->ended=$time;
    $query = "update galaxia_instances set ended=$time where instanceId=".$this->instanceId;
    $this->query($query);    
  }
  
  function getEnded()
  {
    return $this->ended;
  }
  

  
  /*!
  Determines that the current activity
  for this instance is completed adding a
  workitem to the instance
  */
  function complete($activityId=0,$force=false,$addworkitem=true)
  {
  	global $user;
  	global $__activity_completed;
  	
	$__activity_completed = true;
	
  	if(empty($user)) {$theuser='*';} else {$theuser=$user;}
  	
  	if($activityId==0) {
  	  $activityId=$_REQUEST['activityId'];
  	}	
  	
  	// If we are completing a start activity then the instance must 
  	// be created first!
	$type = $this->getOne("select type from galaxia_activities where activityId=$activityId");  	
  	if($type=='start') {
  	  $this->createNewInstance($activityId,$theuser);
  	}
	  	
	// Now set ended
	$now = date("U");
	$query = "update galaxia_instance_activities set ended=$now where activityId=$activityId and instanceId=".$this->instanceId;
	$this->query($query);
  	
  	//Add a workitem to the instance 
  	$iid = $this->instanceId;
  	if($addworkitem) {
		$max = $this->getOne("select max(orderId) from galaxia_workitems where instanceId=$iid");
		if(!$max) {
			$max=1;	  	
		} else {
			$max++;
		}
		$act = $this->_get_instance_activity($activityId);
		if(!$act) {
		  //Then this is a start activity ending
		  $started = $this->getStarted();
		  $putuser = $this->getOwner();
		} else {
    	  $started=$act['started'];
  		  $putuser = $act['user'];
		}
		$ended = date("U");
		$properties = addslashes(serialize($this->properties));
		$query="insert into galaxia_workitems(instanceId, orderId, activityId, started, ended, properties, user)
		values($iid,$max,$activityId,$started,$ended,'$properties','$putuser')";		
		$this->query($query);
		
  	}
  	
  	//Set the status for the instance-activity to completed
  	$this->setActivityStatus($activityId,'completed');
  	
  	//If this and end actt then terminate the instance
  	if($type=='end') {
  	  $this->terminate();
  	  return;
  	}
  	
  	//If the activity ending is autorouted then send to the
  	//activity
  	if($type!='end') {
	  	if( ($force)||($this->getOne("select isAutoRouted from galaxia_activities where activityId=$activityId")=='y')) 	{
	  		// Now determine where to send the instance
	  		$query = "select actToId from galaxia_transitions where actFromId=$activityId";
			$result = $this->query($query);    
			$candidates = Array();
			while($res = $result->fetchRow(DB_FETCHMODE_ASSOC))
			{
		  		$candidates[]=$res['actToId'];
			}  
			if($type=='split') {
			  $first = true;
			  foreach($candidates as $cand) {
  		   	    $this->sendTo($activityId,$cand,$first);
			   	$first = false;
			  }
			} elseif($type=='switch') {
				if(in_array($this->nextActivity,$candidates)) {
					$this->sendTo($activityId,$this->nextActivity);
				} else {
					trigger_error(tra('Fatal error: nextActivity doesn match any candidate in autoruting switch activity'),E_USER_WARNING);
				}
			} else {
			  if(count($candidates)>1) {
			    trigger_error(tra('Fatal error: non-deterministic decision for autorouting activity'),E_USER_WARNING);
			  } else {
			    $this->sendTo($activityId,$candidates[0]);
			  }
			}
	  		
	  	}
  	}
  }
  
  /*!
  Terminates the instance marking the instance
  as completed. This is the end of a process.
  */
  function terminate()
  {
  	//Set the status of the instance to completed
  	$now = date("U");
  	$query = "update galaxia_instances set status='completed', ended=$now where instanceId=".$this->instanceId;
  	$this->query($query);
  	$query = "delete from galaxia_instance_activities where instanceId=".$this->instanceId;
  	$this->query($query);
  	$this->status = 'completed';
  	$this->activities = Array();
  }
  
  function sendTo($from,$activityId,$split=false)
  {
    //1: if we are in a join check
    //if this instance is also in
    //other activity if so do
    //nothing
	$type = $this->getOne("select type from galaxia_activities where activityId=$activityId");
	
	// Verify the existance of a transition
	if(!$this->getOne("select count(*) from galaxia_transitions where actFromId=$from and actToId=$activityId")) {
	  trigger_error(tra('Fatal error: trying to send an instance to an activity but no transition found'),E_USER_WARNING);
	}
	
    
    //try to determine the user or *
    //Use the nextUser
    if($this->nextUser) {
    	$putuser = $this->nextUser;
    } else {
	    $candidates = Array();
	    $query = "select roleId from galaxia_activity_roles where activityId=$activityId";
		$result = $this->query($query);    
		while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
		  $roleId=$res['roleId'];
		  $query2 = "select user from galaxia_user_roles where roleId=$roleId";
		  $result2 = $this->query($query2);     	  
		  while($res2 = $result2->fetchRow(DB_FETCHMODE_ASSOC)) {
		    $candidates[]=$res2['user'];
		  }
		}
		if(count($candidates)==1) {
		  $putuser = $candidates[0];
		} else {
		  $putuser = '*';
		}
	}        
    //update the instance_activities table
    //if not splitting delete first
    //please update started,status,user
    if(!$split) {
      $query = "delete from galaxia_instance_activities where instanceId=".$this->instanceId." and activityId=$from";
      $this->query($query);
	}
    $now = date("U");
    $iid = $this->instanceId;
    $query="replace into galaxia_instance_activities(instanceId,activityId,user,status,started)
    values($iid,$activityId,'$putuser','running',$now)";
    $this->query($query);
    
    //we are now in a new activity
    $this->activities=Array();
    $query = "select * from galaxia_instance_activities where  instanceId=$iid";
	$result = $this->query($query);    
	while($res = $result->fetchRow(DB_FETCHMODE_ASSOC))
	{
	  $this->activities[]=$res;
	}    
	
	if($type=='join') {
	  if(count($this->activities)>1) {
	    // This instance will have to wait!
	    return;
	  }
	}    

     
    //if the activity is not interactive then
	//execute the code for the activity and
	//complete the activity
    $isInteractive = $this->getOne("select isInteractive from galaxia_activities where activityId=$activityId");
    if($isInteractive=='n') {
      // Now execute the code for the activity but we are in a method!
      // so just use an fopen with http mode
      $parsed=parse_url($_SERVER["REQUEST_URI"]);
	  $URI=httpPrefix().$parsed["path"];
	  $parts=explode('/',$URI);
	  $parts[count($parts)-1]="tiki-g-run_activity.php?activityId=$activityId&iid=$iid";
	  $URI=implode('/',$parts);
      $fp = fopen($URI,"r");
      $data = '';
      if(!$fp) {
        trigger_error(tra("Fatal error: cannot execute automatic activity $activityId"),E_USER_WARNING);
        die;
      }
      while(!feof($fp)) {
        $data.=fread($fp,8192);
      }

      /*
      if(!empty($data)) {
		trigger_error(tra("Fatal error: automatic activity produced some output:$data"),E_USER_WARNING);      
      }
      */
      fclose($fp);
      
      // Reload in case the activity did some change
      $this->getInstance($this->instanceId);
      
      $this->complete($activityId);
    }
   
  }
  
  /*! Gets a comment */
  function get_instance_comment($cId)
  {
    $iid = $this->instanceId;
    $query = "select * from galaxia_instance_comments where instanceId=$iid and cId=$cId";
    $result = $this->query($query);
	$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
	return $res;
	      
  }
  
  /*! Inserts or updates an instance comment */
  function replace_instance_comment($cId, $activityId, $activity, $user, $title, $comment)
  {
    if(!$user) $user='"Anonymous"';
    $title=addslashes($title);
    $comment=addslashes($comment);
    $activity=addslashes($activity);
  	$iid= $this->instanceId;
  	if($cId) {
  		$query = "update galaxia_instance_comments set
  		title = '$title',
  		comment = '$comment'
  		where
  		iid=$iid and cId=$cId";
		$this->query($query);	  		
  	} else {
  		$hash = md5($title.$comment);
  		if($this->getOne("select count(*) from galaxia_instance_comments where instanceId=$iid and hash='$hash'")) return false;
  	    $now = date("U");
		$query ="insert into galaxia_instance_comments
		(instanceId, user, activityId, activity, title, comment, timestamp, hash)
		values
		($iid, '$user', $activityId, '$activity', '$title', '$comment', $now, '$hash')";
		$this->query($query);	   	
  	}  
  }
  
  /*!
  Removes an instance comment
  */
  function remove_instance_comment($cId)
  {
    $iid = $this->instanceId;
    $query = "delete from galaxia_instance_comments where cId=$cId and instanceId=$iid";
    $this->query($query);
  }
 
  /*!
  Lists instance comments
  */
  function get_instance_comments()
  {
    $iid = $this->instanceId;
    $query = "select * from galaxia_instance_comments where instanceId=$iid order by timestamp desc";
	$result = $this->query($query);    
	$ret = Array();
	while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {    
		$ret[] = $res;
	}
	return $ret;
  }  
  

  
}
?>