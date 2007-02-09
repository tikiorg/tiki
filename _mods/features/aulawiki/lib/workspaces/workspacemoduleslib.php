<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class WorkspaceModulesLib extends TikiLib {
	function WorkspaceModulesLib($db) {
		$this->TikiLib($db);
	}

	function unassign_workspace_module($moduleId) {
		$query = "delete from `tiki_workspace_modules` where `moduleId`=?";

		$result = $this->query($query,array($moduleId));
	}

	function up_workspace_module($moduleId) {
		$query = "update `tiki_workspace_modules` set `ord`=`ord`-1 where `moduleId`=?";

		$result = $this->query($query,array($moduleId));
	}

	function down_workspace_module($moduleId) {
		$query = "update `tiki_workspace_modules` set `ord`=`ord`+1 where `moduleId`=?";

		$result = $this->query($query,array($moduleId));
	}

	function set_column_workspace_module($moduleId, $position) {
		$query = "update `tiki_workspace_modules` set `position`=? where `moduleId`=?";
		$result = $this->query($query,array($position,$moduleId));
	}

	function assign_workspace_module($name,$position,$ord,$zoneId,$title,$cache_time,$rows,$params,$groups,$style_title,$style_data) {
		$uid = md5(uniqid(rand()));
		$query = 'insert into `tiki_workspace_modules`(`name`,`position`,`ord`,`zoneId`,`title`,`cache_time`,`rows`,`params`,`groups`,`style_title`,`style_data`,`uid`) values(?,?,?,?,?,?,?,?,?,?,?,?)';
		$bindvars = array($name,$position,$ord,$zoneId,$title,$cache_time,$rows,$params,$groups,$style_title,$style_data,$uid);
		$result = $this->query($query, $bindvars);
		return $uid;
	}
	
	function update_workspace_module($name,$position,$ord,$title,$cache_time,$rows,$params,$groups,$style_title,$style_data,$moduleId) {
		$query = 'update `tiki_workspace_modules` set `name`=?,`position`=?,`ord`=?,`title`=?,`cache_time`=?,`rows`=?,`params`=?,`groups`=?,`style_title`=?,`style_data`=? where moduleId=?';
		$bindvars = array($name,$position,$ord,$title,$cache_time,$rows,$params,$groups,$style_title,$style_data,$moduleId);
		$result = $this->query($query, $bindvars);
	}

	function get_workspace_assigned_module($moduleId) {
		$query = "select * from `tiki_workspace_modules` where `moduleId`=?";

		$result = $this->query($query,array($moduleId));

		$res = $result->fetchRow();

		return $res;
	}
	
	function get_workspace_assigned_modules($zoneId) {
		$query = "select * from `tiki_workspace_modules` where `zoneId`=? order by `position` asc,`ord` asc";

		$result = $this->query($query,array($zoneId));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_ws_assigned_modules_by_cols($zoneId) {
		$ret = $this->get_workspace_assigned_modules($zoneId);
		$cols = array();
		foreach ($ret as $key => $module){
			if (!isset($cols[$module["position"]])){
				$cols[$module["position"]] = array();
			}
			$cols[$module["position"]][] = $module;
		}
		return $cols;
	}
	
	function get_workspace_assigned_modules_pos($zoneId, $pos) {
		$query = "select * from `tiki_workspace_modules` where `zoneId`=? and `position`=? order by `ord` asc";

		$result = $this->query($query,array($zoneId, $pos));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function workspace_has_assigned_zones($workspaceId,$type) {
		$query = "select count(`name`) from `tiki_workspace_zones` where `workspaceId`=? and `type`=?";

		$result = $this->getOne($query,array($workspaceId,$type));
		return $result;
	}

    
    /// Toggle module position
    function move_module($moduleId)
    {
        // Get current position
	    $query = "select `position` from `tiki_workspace_modules` where `moduleId`=?";
    	$r = $this->query($query, array($moduleId));
        $res = $r->fetchRow();
        $this->set_column_workspace_module($name, $user, ($res['position'] == 'r' ? 'l' : 'r'));
    }
    
	function get_zones($workspaceId,$type){
		$query = "SELECT * FROM `tiki_workspace_zones` WHERE `workspaceId` = ? and `type`=? order by `ord` asc";
		$result = $this->query($query,array($workspaceId,$type));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[$res["zoneId"]] = $res;
		}

		return $ret;
	}
	
	function get_zone_by_uid($uid){
		$query = "SELECT * FROM `tiki_workspace_zones` WHERE `uid`=? ";
		$result = $this->query($query,array($uid));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}
	
	function get_zone_by_id($id){
		$query = "SELECT * FROM `tiki_workspace_zones` WHERE `zoneId`=? ";
		$result = $this->query($query,array($id));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}
	
	function add_zone($name, $description, $workspaceId, $type, $ord){
		$uid = md5(uniqid(rand()));
		$query = 'INSERT INTO `tiki_workspace_zones` ( `name`, `description`, `workspaceId`, `type`, `ord`, `uid`) VALUES (?, ?, ?, ?, ?, ?)';
				
		$bindvars = array($name, $description, $workspaceId, $type, $ord,$uid);
		$result = $this->query($query, $bindvars);
		return $uid;
	}
	
	function update_zone($zoneId, $name, $description, $workspaceId, $type, $ord){
		$query = "update `tiki_workspace_zones` set `name` = ?, `description` = ?, `workspaceId` = ?, `type` = ?, `ord` = ? WHERE `zoneId` = ? ";

		$result = $this->query($query,array($name, $description, $workspaceId, $type, $ord,$zoneId));
	}
	
	function delete_zone($zoneId){
		$query = "delete from `tiki_workspace_zones` where `zoneId`=?";

		$result = $this->query($query,array($zoneId));
	}	
	
	function delete_assigned_zone_modules($zoneId){
		$modules = $this->get_workspace_assigned_modules($zoneId);	
		foreach ($modules as $key => $module) {
				$this->unassign_workspace_module($module["moduleId"]);
	}
}
}

$wsmoduleslib = new WorkspaceModulesLib($dbTiki);

?>