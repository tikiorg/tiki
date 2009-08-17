<?php
include_once (GALAXIA_LIBRARY.'/src/common/Base.php');
//!! Instance
//! A class representing a process instance.
/*!
This class represents a process instance, it is used when any activity is
executed. The $instance object is created representing the instance of a
process being executed in the activity or even a to-be-created instance
if the activity is a start activity.
*/
class Instance extends Base {
  var $properties = Array();
  var $owner = '';
  var $status = '';
  var $started;
  var $name = '';
  var $nextActivity;
  var $nextUser;
  var $ended;
  /// Array of asocs(activityId,status,started,user)
  var $activities = Array();
  var $pId;
  var $instanceId = 0;
  /// An array of workitem ids
  var $workitems = Array(); 

  function Instance($db) {
    $this->db = $db;
  }

  /*!
  Method used to load an instance data from the database.
  */
  function getInstance($instanceId) {
    // Get the instance data
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instances` where `instanceId`=?";
    $result = $this->query($query,array((int)$instanceId));
    if(!$result->numRows()) return false;
    $res = $result->fetchRow();

    //Populate 
    $this->properties = unserialize($res['properties']);
    $this->status = $res['status'];
    $this->pId = $res['pId'];
    $this->instanceId = $res['instanceId'];
    $this->owner = $res['owner'];
    $this->started = $res['started'];
    $this->name = $res['name'];
    $this->ended = $res['ended'];
    $this->nextActivity = $res['nextActivity'];
    $this->nextUser = $res['nextUser'];
    // Get the activities where the instance is (ids only is ok)
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_activities` where  `instanceId`=?";
    $result = $this->query($query,array((int)$instanceId));    
    while($res = $result->fetchRow()) {
      $this->activities[]=$res;
    }    
  }
  
  /*! 
  Sets the next activity to be executed, if the current activity is
  a switch activity the complete() method will use the activity setted
  in this method as the next activity for the instance. 
  Note that this method receives an activity name as argument. (Not an Id)
  */
  function setNextActivity($actname) {
    $pId = $this->pId;
    $actname=trim($actname);
    $aid = $this->getOne("select `activityId` from `".GALAXIA_TABLE_PREFIX."activities` where `pId`=? and `name`=?",array($pId,$actname));
    if(!$this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."activities` where `activityId`=? and `pId`=?",array($aid,$pId))) {
      trigger_error(tra('Fatal error: setting next activity to an unexisting activity'),E_USER_WARNING);
    }
    $this->nextActivity=$aid;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `nextActivity`=? where `instanceId`=?";
    $this->query($query,array((int)$aid,(int)$this->instanceId));
  }

  /*!
  This method can be used to set the user that must perform the next 
  activity of the process. this effectively "assigns" the instance to
  some user.
  */
  function setNextUser($user) {
    $pId = $this->pId;
    $this->nextUser = $user;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `nextUser`=? where `instanceId`=?";
    $this->query($query,array($user,(int)$this->instanceId));
  }
 
  /*!
   \private
   Creates a new instance.
   This method is called in start activities when the activity is completed
   to create a new instance representing the started process.
  */
  function _createNewInstance($activityId,$user) {
    // Creates a new instance setting up started,ended,user
    // and status
    $pid = $this->getOne("select `pId` from `".GALAXIA_TABLE_PREFIX."activities` where `activityId`=?",array((int)$activityId));
    $this->status = 'active';
    $this->nextActivity = 0;
    $this->setNextUser('');
    $this->pId = $pid;
    $now = date("U");
    $this->started=$now;
    $this->owner = $user;
    $props=serialize($this->properties);
    $query = "insert into `".GALAXIA_TABLE_PREFIX."instances`(`started`,`ended`,`status`,`name`,`pId`,`owner`,`properties`) values(?,?,?,?,?,?,?)";
    $this->query($query,array($now,0,'active',$this->name,$pid,$user,$props));
    $this->instanceId = $this->getOne("select max(`instanceId`) from `".GALAXIA_TABLE_PREFIX."instances` where `started`=? and `owner`=?",array((int)$now,$user));
    $iid=$this->instanceId;
    
    // Now update the properties!
    $props = serialize($this->properties);
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `properties`=? where `instanceId`=?";
    $this->query($query,array($props,(int)$iid));

    // Then add in ".GALAXIA_TABLE_PREFIX."instance_activities an entry for the
    // activity the user and status running and started now
    $query = "insert into `".GALAXIA_TABLE_PREFIX."instance_activities`(`instanceId`,`activityId`,`user`,`started`,`status`) values(?,?,?,?,?)";
    $this->query($query,array((int)$iid,(int)$activityId,$user,(int)$now,'running'));
  }
  
  /*! 
  Sets a property in this instance. This method is used in activities to
  set instance properties. Instance properties are inemdiately serialized.
  */
  function set($name,$value) {
    $this->properties[$name] = $value;
    $props = serialize($this->properties);
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `properties`=? where `instanceId`=?";
    $this->query($query,array($props,$this->instanceId));
  }
  
  /*! 
  Gets the value of an instance property.
  */
  function get($name) {
    if(isset($this->properties[$name])) {
      return $this->properties[$name];
    } else {
      return false;
    }
  }
  
  /*! 
  Returns an array of asocs describing the activities where the instance
  is present, can be more than one activity if the instance was "splitted"
  */
  function getActivities() {
    return $this->activities;
  }
  
  /*! 
  Gets the instance status can be
  'completed', 'active', 'aborted' or 'exception'
  */
  function getStatus() {
    return $this->status;
  }
  
  /*! 
  Sets the instance status , the value can be:
  'completed', 'active', 'aborted' or 'exception'
  */
  function setStatus($status) {
    $this->status = $status; 
    // and update the database
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `status`=? where `instanceId`=?";
    $this->query($query,array($status,(int)$this->instanceId));  
  }
  
  /*!
  Returns the instanceId
  */
  function getInstanceId() {
    return $this->instanceId;
  }
  
  /*! 
  Returns the processId for this instance
  */
  function getProcessId() {
    return $this->pId;
  }
  
  /*! 
  Returns the name associated to the instance
  */
  function getName() {
    return $this->name;
  }
  
    /*! 
  Sets the instance name user 
  */
  function setName($name) {
    $this->name = $name;
    // save database
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `name`=? where `instanceId`=?";
    $this->query($query,array($name,(int)$this->instanceId));  
  }
  
  /*! 
  Returns the user that created the instance
  */
  function getOwner() {
    return $this->owner;
  }
  
  /*! 
  Sets the instance creator user 
  */
  function setOwner($user) {
    $this->owner = $user;
    // save database
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `owner`=? where `instanceId`=?";
    $this->query($query,array($this->owner,(int)$this->instanceId));  
  }
  
  /*!
  Sets the user that must execute the activity indicated by the activityId.
  Note that the instance MUST be present in the activity to set the user,
  you can't program who will execute an activity.
  */
  function setActivityUser($activityId,$theuser) {
    if(empty($theuser)) $theuser='*';
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activityId']==$activityId) {
        $this->activities[$i]['user']=$theuser;
        $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `user`=? where `activityId`=? and `instanceId`=?";

        $this->query($query,array($theuser,(int)$activityId,(int)$this->instanceId));
      }
    }  
  }
  
  /*!
  Returns the user that must execute or is already executing an activity
  wherethis instance is present.
  */  
  function getActivityUser($activityId) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activityId']==$activityId) {
        return $this->activities[$i]['user'];
      }
    }  
    return false;
  }

  /*!
  Sets the status of the instance in some activity, can be
  'running' or 'completed'
  */  
  function setActivityStatus($activityId,$status) {
//    for($i=0;$i<count($this->activities);$i++) {
//      if($this->activities[$i]['activityId']==$activityId) {
//        $this->activities[$i]['status']=$status;
        $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `status`=? where `activityId`=? and `instanceId`=?";
        $this->query($query,array($status,(int)$activityId,(int)$this->instanceId));
//      }
 //   }  
  }
  
  
  /*!
  Gets the status of the instance in some activity, can be
  'running' or 'completed'
  */
  function getActivityStatus($activityId) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activityId']==$activityId) {
        return $this->activities[$i]['status'];
      }
    }  
    return false;
  }
  
  /*!
  Resets the start time of the activity indicated to the current time.
  */
  function setActivityStarted($activityId) {
    $now = date("U");
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activityId']==$activityId) {
        $this->activities[$i]['started']=$now;
        $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `started`=? where `activityId`=? and `instanceId`=?";
        $this->query($query,array($now,(int)$activityId,(int)$this->instanceId));
      }
    }  
  }
  
  /*!
  Gets the Unix timestamp of the starting time for the given activity
  */
  function getActivityStarted($activityId) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activityId']==$activityId) {
        return $this->activities[$i]['started'];
      }
    }  
    return false;
  }
  
  /*!
  \private
  Gets an activity from the list of activities of the instance
  */
  function _get_instance_activity($activityId) {
    for($i=0;$i<count($this->activities);$i++) {
      if($this->activities[$i]['activityId']==$activityId) {
        return $this->activities[$i];
      }
    }  
    return false;
  }

  /*!
  Sets the time where the instance was started.    
  */
  function setStarted($time) {
    $this->started=$time;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `started`=? where `instanceId`=?";
    $this->query($query,array((int)$time,(int)$this->instanceId));    
  }
  
  /*!
  Gets the time where the instance was started (Unix timestamp)
  */
  function getStarted() {
    return $this->started;
  }
  
  /*!
  Sets the end time of the instance (when the process was completed)
  */
  function setEnded($time) {
    $this->ended=$time;
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `ended`=? where `instanceId`=?";
    $this->query($query,array((int)$time,(int)$this->instanceId));    
  }
  
  /*!
  Gets the end time of the instance (when the process was completed)
  */
  function getEnded() {
    return $this->ended;
  }
  
  /*!
  Completes an activity, normally from any activity you should call this
  function without arguments.
  The arguments are explained just in case.
  $activityId is the activity that is being completed, when this is not
  passed the engine takes it from the $_REQUEST array,all activities
  are executed passing the activityId in the URI.
  $force indicates that the instance must be routed no matter if the
  activity is auto-routing or not. This is used when "sending" an
  instance from a non-auto-routed activity to the next activity.
  $addworkitem indicates if a workitem should be added for the completed
  activity.
  YOU MUST NOT CALL complete() for non-interactive activities since
  the engine does automatically complete automatic activities after
  executing them.
  */
  function complete($activityId = 0, $force = false, $addworkitem = true) {
    global $user;
    global $__activity_completed;
    
    $__activity_completed = true;

	$isInteractive = $this->getOne("SELECT `isInteractive` FROM `" . GALAXIA_TABLE_PREFIX . "activities` WHERE `activityId`=?", array((int)$activityId));

    if ($isInteractive == 'n') {
		$theuser = 'Not Interactive';
	} else {
		$theuser = (empty($user)) ? '*' : $user;
	}

    if ($activityId == 0) {
		$activityId = $_REQUEST['activityId'];
    }  

    // If we are completing a start activity then the instance must be created first!
    $type = $this->getOne("SELECT `type` FROM `" . GALAXIA_TABLE_PREFIX . "activities` WHERE `activityId`=?", array((int)$activityId));

	if($type == 'start') {
		$this->_createNewInstance((int)$activityId, $theuser);
    }

	// Now that we have an instance, we can set $activityId as 'completed'
    $now = date("U");
    $query = "UPDATE `" . GALAXIA_TABLE_PREFIX . "instance_activities` SET `ended`=?, `user`=? WHERE `activityId`=? AND `instanceId`=?";
    $this->query($query, array((int)$now, $theuser, (int)$activityId, (int)$this->instanceId));

    //Add a workitem to the instance 
    $iid = $this->instanceId;

    if ($addworkitem) {
		$max = $this->getOne("SELECT MAX(`orderId`) FROM `".GALAXIA_TABLE_PREFIX."workitems` WHERE `instanceId`=?", array((int)$iid));

		if (!$max) {
			$max = 1;
		} else {
			$max++;
		}

		$act = $this->_get_instance_activity($activityId);

		if(!$act) {
			//Then this is a start activity ending
			$started = $this->getStarted();
			$putuser = $this->getOwner();
		} else {
			$started = $act['started'];
			$putuser = $act['user'];
		}

		$ended = date("U");
		$properties = serialize($this->properties);
		$query = "INSERT INTO `" . GALAXIA_TABLE_PREFIX . "workitems` (`instanceId`, `orderId`, `activityId`, `started`, `ended`, `properties`, `user`) VALUES (?,?,?,?,?,?,?)";
		$this->query($query, array((int)$iid, (int)$max, (int)$activityId, (int)$started, (int)$ended, $properties, $theuser));
    }
    
    //Set the status for the instance-activity to 'completed'
    $this->setActivityStatus($activityId, 'completed');
    
    //If this an end activity then terminate the instance
    if ($type == 'end') {
		$this->terminate();
		return;
    }
    
    //If the activity ending is autorouted then send to the next activity
    if ($type != 'end') {
		if (($force) || ($this->getOne("SELECT `isAutoRouted` FROM `" . GALAXIA_TABLE_PREFIX . "activities` WHERE `activityId`=?", array($activityId)) == 'y')) {
			// Now determine where to send the instance
			$query = "SELECT `actToId` FROM `" . GALAXIA_TABLE_PREFIX . "transitions` WHERE `actFromId`=?";
			$result = $this->query($query, array((int)$activityId));

			$candidates = Array();

			while ($res = $result->fetchRow()) {
				$candidates[] = $res['actToId'];
			}  

			if ($type == 'split') {
				$first = true;

				foreach ($candidates as $cand) {
					$this->sendTo($activityId, $cand, $first);
					$first = false;
				}
			} elseif ($type == 'switch') {
				if (in_array($this->nextActivity, $candidates)) {
					$this->sendTo((int)$activityId, (int)$this->nextActivity);
				} else {
					trigger_error(tra('Fatal error: nextActivity does not match any candidate in autorouting switch activity'), E_USER_WARNING);
				}
			} else {
				if (count($candidates) > 1) {
					trigger_error(tra('Fatal error: non-deterministic decision for autorouting activity'), E_USER_WARNING);
				} else {
					$this->sendTo((int)$activityId, (int)$candidates[0]);
				}
			}
		}
    }

    return $this->instanceId;
  }
  
  /*!
  Aborts an activity and terminates the whole instance. We still create a workitem to keep track
  of where in the process the instance was aborted
  */
  function abort($activityId=0,$theuser = '',$addworkitem=true) {
    if(empty($theuser)) {
      global $user;
      if (empty($user)) {$theuser='*';} else {$theuser=$user;}
    }
    
    if($activityId==0) {
      $activityId=$_REQUEST['activityId'];
    }  
    
    // If we are completing a start activity then the instance must 
    // be created first!
    $type = $this->getOne("select `type` from `".GALAXIA_TABLE_PREFIX."activities` where `activityId`=?",array((int)$activityId));    
    if($type=='start') {
      $this->_createNewInstance((int)$activityId,$theuser);
    }
      
    // Now set ended
    $now = date("U");
    $query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `ended`=? where `activityId`=? and `instanceId`=?";
    $this->query($query,array((int)$now,(int)$activityId,(int)$this->instanceId));
    
    //Add a workitem to the instance 
    $iid = $this->instanceId;
    if($addworkitem) {
      $max = $this->getOne("select max(`orderId`) from `".GALAXIA_TABLE_PREFIX."workitems` where `instanceId`=?",array((int)$iid));
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
      $properties = serialize($this->properties);
      $query="insert into `".GALAXIA_TABLE_PREFIX."workitems`(`instanceId`,`orderId`,`activityId`,`started`,`ended`,`properties`,`user`) values(?,?,?,?,?,?,?)";    
      $this->query($query,array((int)$iid,(int)$max,(int)$activityId,(int)$started,(int)$ended,$properties,$putuser));
    }
    
    //Set the status for the instance-activity to aborted
// TODO: support 'aborted' if we keep activities after termination some day
    //$this->setActivityStatus($activityId,'aborted');

    // terminate the instance with status 'aborted'
    $this->terminate('aborted');
  }
  
  /*!
  Terminates the instance marking the instance and the process
  as completed. This is the end of a process.
  Normally you should not call this method since it is automatically
  called when an end activity is completed.
  */
  function terminate($status = 'completed') {
    //Set the status of the instance to completed
    $now = date("U");
    $query = "update `".GALAXIA_TABLE_PREFIX."instances` set `status`=?, `ended`=? where `instanceId`=?";
    $this->query($query,array($status,(int)$now,(int)$this->instanceId));
    //$query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instanceId`=?";
    //$this->query($query,array((int)$this->instanceId));
    $this->status = $status;
    $this->activities = Array();
  }
  
  
  /*!
  Sends the instance from some activity to another activity.
  You should not call this method unless you know very very well what
  you are doing.
  */
  function sendTo($from, $activityId, $split = false) {
    //1: if we are in a join check if this instance is also in other activity
	//if so do nothing
    $type = $this->getOne("SELECT `type` FROM `" . GALAXIA_TABLE_PREFIX . "activities` WHERE `activityId`=?", array((int)$activityId));

    // Verify the existance of a transition
    if (!$this->getOne("SELECT count(*) FROM `" . GALAXIA_TABLE_PREFIX . "transitions` WHERE `actFromId`=? AND `actToId`=?",array($from, (int)$activityId))) {
		trigger_error(tra('Fatal error: trying to send an instance to an activity but no transition found'), E_USER_WARNING);
    }

    //Use the nextUser
    if ($this->nextUser) {
		$putuser = $this->nextUser;
    } else {
		//Try to determine the user or *
		$candidates = Array();
		$query = "SELECT `roleId` FROM `" . GALAXIA_TABLE_PREFIX . "activity_roles` WHERE `activityId`=?";
		$result = $this->query($query, array((int)$activityId));

		while ($res = $result->fetchRow()) {
			$roleId = $res['roleId'];
			$query2 = "SELECT `user` FROM `" . GALAXIA_TABLE_PREFIX . "user_roles` WHERE `roleId`=?";
			$result2 = $this->query($query2, array((int)$roleId)); 

			while ($res2 = $result2->fetchRow()) {
				$candidates[] = $res2['user'];
			}
		}

		$putuser = (count($candidates) == 1) ? $candidates[0] : '*';
    }        

	$now = date("U");
    $iid = $this->instanceId;
	
	// Test if the join activity has preceding activities that are not completed yet
	if ($type == 'join') {
		// Calculate 1)how many incoming transitions the activity has, and 2)how many of those are completed
		$querycant = "SELECT COUNT(*) FROM `" . GALAXIA_TABLE_PREFIX . "transitions` WHERE actToId = ?";
		$querycomp = "SELECT COUNT(*) FROM `" . GALAXIA_TABLE_PREFIX . "transitions` tr " . "INNER JOIN "
		             . GALAXIA_TABLE_PREFIX . "instance_activities gia ON tr.actFromId=gia.activityId
                     WHERE tr.pid=? AND tr.actToId=? AND gia.instanceId=? AND gia.status = ?";

		$transcant = $this->getone($querycant, array($activityId));
		$transcomp = $this->getone($querycomp, array($this->pId, $activityId, $iid, 'completed'));

		// if there are still preceding activities not completed, STOP
		if ($nb = $transcant - $transcomp) {
			//echo 'Pending preceding activities = ' . $nb;
			return;
		}
	}
	
    $query = "DELETE FROM `" . GALAXIA_TABLE_PREFIX . "instance_activities` WHERE `instanceId`=? AND `activityId`=?";
    $this->query($query, array((int)$iid, (int)$activityId));

    $query = "INSERT INTO `" . GALAXIA_TABLE_PREFIX . "instance_activities` (`instanceId`, `activityId`, `user`, `status`, `started`) VALUES (?,?,?,?,?)";
    $this->query($query, array((int)$iid, (int)$activityId, $putuser, 'running', (int)$now));

	// Check whether the activity we're sending the instance to is interactive
	$isInteractive = $this->getOne("SELECT `isInteractive` FROM `" . GALAXIA_TABLE_PREFIX . "activities` WHERE `activityId`=?", array((int)$activityId));

    //if the activity is not interactive then execute its code and complete it
    if ($isInteractive == 'n') {
		// These are necessary to determine if the activity needs to be recompiled
		$proc = new Process($this->db);
		$proc->getProcess($this->pId);
		$baseact = new BaseActivity($this->db);
		$act = $baseact->getActivity($activityId);

		// Get paths for original and compiled activity code
		$origcode = 'lib/Galaxia/processes/' . $proc->getNormalizedName(). '/code/activities/' . $act->getNormalizedName() . '.php';
		$compcode = 'lib/Galaxia/processes/' . $proc->getNormalizedName() . '/compiled/' . $act->getNormalizedName(). '.php';

		// Check whether the activity code is newer than its compiled counterpart,
		// i.e. check if the source code was changed; if so, we need to recompile
		if (filemtime($origcode) > filemtime($compcode)) {
			// Recompile
			include_once('lib/Galaxia/src/ProcessManager/ActivityManager.php');
			include_once('lib/Galaxia/src/ProcessManager/ProcessManager.php');
			$am = new ActivityManager($this->db);
			$am->compile_activity($this->pId, $activityId);
		}

		// Now execute the code for the activity (function galaxia_execute_activity
		// is defined in lib/Galaxia/config.php)
		galaxia_execute_activity($activityId, $iid, 1);

		// Reload in case the activity did some change
		$this->getInstance($this->instanceId);
		$this->complete($activityId);
	}
  }
  
  /*! 
  Gets a comment for this instance 
  */
  function get_instance_comment($cId) {
    $iid = $this->instanceId;
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_comments` where `instanceId`=? and `cId`=?";
    $result = $this->query($query,array((int)$iid,(int)$cId));
    $res = $result->fetchRow();
    return $res;
  }
  
  /*! 
  Inserts or updates an instance comment 
  */
  function replace_instance_comment($cId, $activityId, $activity, $user, $title, $comment) {
    if (!$user) {
      $user = 'Anonymous';
    }
    $iid = $this->instanceId;
    if ($cId) {
      $query = "update `".GALAXIA_TABLE_PREFIX."instance_comments` set `title`=?,`comment`=? where `instanceId`=? and `cId`=?";
      $this->query($query,array($title,$comment,(int)$iid,(int)$cId));
    } else {
      $hash = md5($title.$comment);
      if ($this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."instance_comments` where `instanceId`=? and `hash`=?",array($iid,$hash))) {
        return false;
      }
      $now = date("U");
      $query ="insert into `".GALAXIA_TABLE_PREFIX."instance_comments`(`instanceId`,`user`,`activityId`,`activity`,`title`,`comment`,`timestamp`,`hash`) values(?,?,?,?,?,?,?,?)";
      $this->query($query,array((int)$iid,$user,(int)$activityId,$activity,$title,$comment,(int)$now,$hash));
    }  
  }
  
  /*!
  Removes an instance comment
  */
  function remove_instance_comment($cId) {
    $iid = $this->instanceId;
    $query = "delete from `".GALAXIA_TABLE_PREFIX."instance_comments` where `cId`=? and `instanceId`=?";
    $this->query($query,array((int)$cId,(int)$iid));
  }
 
  /*!
  Lists instance comments
  */
  function get_instance_comments($aid) {
    $iid = $this->instanceId;
    $query = "select * from `".GALAXIA_TABLE_PREFIX."instance_comments` where `instanceId`=? and `activityId`=? order by ".$this->convert_sortmode("timestamp_desc");
    $result = $this->query($query,array((int)$iid,(int)$aid));    
    $ret = Array();
    while($res = $result->fetchRow()) {    
      $ret[] = $res;
    }
    return $ret;
  }

}
?>
