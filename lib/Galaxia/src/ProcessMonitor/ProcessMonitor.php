<?php
include_once(GALAXIA_LIBRARY.'/src/common/Base.php');
//!! ProcessMonitor
//! ProcessMonitor class
/*!
This class provides methods for use in typical monitoring scripts
*/
class ProcessMonitor extends Base {

  function monitor_stats() {
    $res = Array();
    $res['active_processes'] = $this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."processes` where `isActive`=?",array('y'));
    $res['processes'] = $this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."processes`");
    $result = $this->query("select distinct(`pId`) from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?",array('active'));
		// echo"<pre>";print_r($result);echo"</pre>";
    $res['running_processes'] = $result->numRows();
    $res['active_instances'] = $this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?",array("active"));
    $res['completed_instances'] = $this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?",array("completed"));
    $res['exception_instances'] = $this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?",array("exception"));
    $res['aborted_instances'] = $this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?",array("aborted"));
    return $res;
  }
  
  function update_instance_status($iid,$status) {
  	$query = "update `".GALAXIA_TABLE_PREFIX."instances` set `status`=? where `instanceId`=?";
  	$this->query($query,array($status,$iid));
  }
  
  function update_instance_activity_status($iid,$activityId,$status) {
  	$query = "update `".GALAXIA_TABLE_PREFIX."instance_activities` set `status`=? where `instanceId`=? and `activityId`=?";
  	$this->query($query,array($status,$iid,$activityId));
  
  }
  
  function remove_instance($iid) {
		$query = "delete from `".GALAXIA_TABLE_PREFIX."workitems` where `instanceId`=?";
		$this->query($query,array($iid));
		$query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instanceId`=?";
		$this->query($query,array($iid));
		$query = "delete from `".GALAXIA_TABLE_PREFIX."instances` where `instanceId`=?";
		$this->query($query,array($iid));  
  }
  
  function remove_aborted() {
	
	$query="select `instanceId` from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?";
	$result = $this->query($query,array('aborted'));
	while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {	
		$iid = $res['instanceId'];
		$query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instanceId`=?";
	  $this->query($query,array($iid));
	  $query = "delete from `".GALAXIA_TABLE_PREFIX."workitems` where `instanceId`=?";
	  $this->query($query,array($iid));  
	}
	$query = "delete from `".GALAXIA_TABLE_PREFIX."instances` where `status`=?";
	$this->query($query,array('aborted'));
	
  }

  function remove_all($pId) {
		$query="select `instanceId` from `".GALAXIA_TABLE_PREFIX."instances` where `pId`=?";
		$result = $this->query($query,array($pId));
		while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {	
			$iid = $res['instanceId'];
			$query = "delete from `".GALAXIA_TABLE_PREFIX."instance_activities` where `instanceId`=?";
			$this->query($query,array($iid));
			$query = "delete from `".GALAXIA_TABLE_PREFIX."workitems` where `instanceId`=?";
			$this->query($query,array($iid));  
		}
		$query = "delete from `".GALAXIA_TABLE_PREFIX."instances` where `pId`=?";
		$this->query($query,array($pId));
  }

  
  function monitor_list_processes($offset,$maxRecords,$sort_mode,$find,$where='') {
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
    $query = "select * from ".GALAXIA_TABLE_PREFIX."processes $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from ".GALAXIA_TABLE_PREFIX."processes $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Number of active instances
      $res['active_instances']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instances where status='active' and pId=".$res['pId']);
      // Number of exception instances
      $res['exception_instances']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instances where status='exception' and pId=".$res['pId']);
      // Number of completed instances
      $res['completed_instances']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instances where status='completed' and pId=".$res['pId']);
      // Number of aborted instances
      $res['aborted_instances']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instances where status='aborted' and pId=".$res['pId']);
      $res['all_instances']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."instances where pId=".$res['pId']);
      // Number of activities
      $res['activities']=$this->getOne("select count(*) from ".GALAXIA_TABLE_PREFIX."activities where pId=".$res['pId']);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function monitor_list_activities($offset,$maxRecords,$sort_mode,$find,$where='') {
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
    $query = "select * from ".GALAXIA_TABLE_PREFIX."activities $mid order by $sort_mode limit $offset,$maxRecords";
    $query_cant = "select count(*) from ".GALAXIA_TABLE_PREFIX."activities $mid";
    $result = $this->query($query);
    $cant = $this->getOne($query_cant);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      // Number of active instances
      $aid = $res['activityId'];
      $res['active_instances']=$this->getOne("select count(gi.instanceId) from ".GALAXIA_TABLE_PREFIX."instances gi,".GALAXIA_TABLE_PREFIX."instance_activities gia where gi.instanceId=gia.instanceId and gia.activitYId=$aid and gi.status='active' and pId=".$res['pId']);
      $res['completed_instances']=$this->getOne("select count(gi.instanceId) from ".GALAXIA_TABLE_PREFIX."instances gi,".GALAXIA_TABLE_PREFIX."instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gi.status='completed' and pId=".$res['pId']);
      $res['aborted_instances']=$this->getOne("select count(gi.instanceId) from ".GALAXIA_TABLE_PREFIX."instances gi,".GALAXIA_TABLE_PREFIX."instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gi.status='aborted' and pId=".$res['pId']);
      $res['exception_instances']=$this->getOne("select count(gi.instanceId) from ".GALAXIA_TABLE_PREFIX."instances gi,".GALAXIA_TABLE_PREFIX."instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gi.status='exception' and pId=".$res['pId']);
	  $res['act_running_instances']=$this->getOne("select count(gi.instanceId) from ".GALAXIA_TABLE_PREFIX."instances gi,".GALAXIA_TABLE_PREFIX."instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gia.status='running' and pId=".$res['pId']);      
      $res['act_completed_instances']=$this->getOne("select count(gi.instanceId) from ".GALAXIA_TABLE_PREFIX."instances gi,".GALAXIA_TABLE_PREFIX."instance_activities gia where gi.instanceId=gia.instanceId and gia.activityId=$aid and gia.status='completed' and pId=".$res['pId']);
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }

  function monitor_list_instances($offset,$maxRecords,$sort_mode,$find,$where='',$wherevars) {
    if($find) {
			$findesc = $this->qstr('%'.$find.'%');
      $mid=" where ((`properties` like ?)";
			$wherevars[] = $findesc;
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
    $query = "select gp.`pId`, ga.`isInteractive`, gi.`owner`, gp.`name` as `procname`, gp.`version`, ga.`type`,";
		$query.= " ga.`activityId`, ga.`name`, gi.`instanceId`, gi.`status`, gia.`activityId`, gia.`user`, gi.`started`, gia.`status` as actstatus ";
		$query.=" from `".GALAXIA_TABLE_PREFIX."instances` gi LEFT JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON gi.`instanceId`=gia.`instanceId` ";
		$query.= "LEFT JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gia.`activityId` = ga.`activityId` ";
		$query.= "LEFT JOIN `".GALAXIA_TABLE_PREFIX."processes` gp ON gp.`pId`=gi.`pId` $mid order by ".$this->convert_sortmode($sort_mode);   

    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."instances` gi LEFT JOIN `".GALAXIA_TABLE_PREFIX."instance_activities` gia ON gi.`instanceId`=gia.`instanceId` ";
		$query_cant.= "LEFT JOIN `".GALAXIA_TABLE_PREFIX."activities` ga ON gia.`activityId` = ga.`activityId` LEFT JOIN `".GALAXIA_TABLE_PREFIX."processes` gp ON gp.`pId`=gi.`pId` $mid";
    $result = $this->query($query,$wherevars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$wherevars);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $iid = $res['instanceId'];
      $res['workitems']=$this->getOne("select count(*) from `".GALAXIA_TABLE_PREFIX."workitems` where `instanceId`=?",array($iid));
      $ret[] = $res;
    }
    $retval = Array();
    $retval["data"] = $ret;
    $retval["cant"] = $cant;
    return $retval;
  }


  function monitor_list_all_processes($sort_mode) {
    $query = "select distinct(`name`),`pId` from `".GALAXIA_TABLE_PREFIX."processes` order by ".$this->convert_sortmode($sort_mode);
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res;
    }
    return $ret;
  }
  
  function monitor_list_statuses() {
    $query = "select distinct(`status`) from `".GALAXIA_TABLE_PREFIX."instances`";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['status'];
    }
    return $ret;
  }
  
  function monitor_list_users() {
    $query = "select distinct(`user`) from `".GALAXIA_TABLE_PREFIX."instance_activities`";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['user'];
    }
    return $ret;
  }

  function monitor_list_wi_users() {
    $query = "select distinct(`user`) from `".GALAXIA_TABLE_PREFIX."workitems`";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['user'];
    }
    return $ret;
  }

  
  function monitor_list_owners() {
    $query = "select distinct(`owner`) from `".GALAXIA_TABLE_PREFIX."instances`";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['owner'];
    }
    return $ret;
  }
  
  
  function monitor_list_activity_types() {
    $query = "select distinct(`type`) from `".GALAXIA_TABLE_PREFIX."activities`";
    $result = $this->query($query);
    $ret = Array();
    while($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
      $ret[] = $res['type'];
    }
    return $ret;  
  }
  
  function monitor_get_workitem($itemId) {
    $query = "select gw.`orderId`,ga.`name`,ga.`type`,ga.`isInteractive`,gp.`name` as `procname`,gp.`version`,";
		$query.= "gw.`itemId`,gw.`properties`,gw.`user`,`started`,`ended`-`started` as duration ";
		$query.= "from `".GALAXIA_TABLE_PREFIX."workitems` gw,`".GALAXIA_TABLE_PREFIX."activities` ga,`".GALAXIA_TABLE_PREFIX."processes` gp where ga.`activityId`=gw.`activityId` and ga.`pId`=gp.`pId` and `itemId`=?";
    $result = $this->query($query, array($itemId));
    $res = $result->fetchRow(DB_FETCHMODE_ASSOC);
    $res['properties'] = unserialize($res['properties']);
    return $res;
  }

  // List workitems per instance, remove workitem, update_workitem
  function monitor_list_workitems($offset,$maxRecords,$sort_mode,$find,$where='',$wherevars=array()) {
		$mid = '';
    if ($where) {
			$mid.= " and ($where) ";
    }
    if($find) {
			$findesc = $this->qstr('%'.$find.'%');
      $mid.=" and ((`properties` like ?) or (`name` like ?))";
			$wherevars[] = $findesc;
			$wherevars[] = $findesc;
    }
    $query = "select `itemId`,`ended`-`started` as duration,ga.`isInteractive`, ga.`type`,gp.`name` as procname,gp.`version`,ga.`name` as actname,";
		$query.= "ga.`activityId`,`instanceId`,`orderId`,`properties`,`started`,`ended`,`user` from `".GALAXIA_TABLE_PREFIX."workitems` gw,`".GALAXIA_TABLE_PREFIX."activities` ga,`".GALAXIA_TABLE_PREFIX."processes` gp ";
		$query.= "where gw.`activityId`=ga.`activityId` and ga.`pId`=gp.`pId` $mid order by gp.`pId` desc,".$this->convert_sortmode($sort_mode);
    $query_cant = "select count(*) from `".GALAXIA_TABLE_PREFIX."workitems` gw,`".GALAXIA_TABLE_PREFIX."activities` ga,`".GALAXIA_TABLE_PREFIX."processes` gp where gw.`activityId`=ga.`activityId` and ga.`pId`=gp.`pId` $mid";
    $result = $this->query($query,$wherevars,$maxRecords,$offset);
    $cant = $this->getOne($query_cant,$wherevars);
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
