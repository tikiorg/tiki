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
		if (!$db) {
			die ("Invalid db object passed to UserModulesLib constructor");
		}

		$this->db = $db;
	}

	function unassign_workspace_module($moduleId) {
		$query = "delete from `aulawiki_workspace_modules` where `moduleId`=?";

		$result = $this->query($query,array($moduleId));
	}

	function up_workspace_module($moduleId) {
		$query = "update `aulawiki_workspace_modules` set `ord`=`ord`-1 where `moduleId`=?";

		$result = $this->query($query,array($moduleId));
	}

	function down_workspace_module($moduleId) {
		$query = "update `aulawiki_workspace_modules` set `ord`=`ord`+1 where `moduleId`=?";

		$result = $this->query($query,array($moduleId));
	}

	function set_column_workspace_module($moduleId, $position) {
		$query = "update `aulawiki_workspace_modules` set `position`=? where `moduleId`=?";
		$result = $this->query($query,array($position,$moduleId));
	}

	function assign_workspace_module($name,$position,$ord,$type,$workspaceId,$title,$cache_time,$rows,$params,$groups) {
		$uid = md5(uniqid(rand()));
		$query = 'insert into `aulawiki_workspace_modules`(`name`,`position`,`ord`,`type`,`workspaceId`,`title`,`cache_time`,`rows`,`params`,`groups`,`uid`) values(?,?,?,?,?,?,?,?,?,?,?)';
		$bindvars = array($name,$position,$ord,$type,$workspaceId,$title,$cache_time,$rows,$params,$groups,$uid);
		$result = $this->query($query, $bindvars);
		return $uid;
	}
	
	function update_workspace_module($name,$position,$ord,$title,$cache_time,$rows,$params,$groups,$moduleId) {
		$query = 'update `aulawiki_workspace_modules` set `name`=?,`position`=?,`ord`=?,`title`=?,`cache_time`=?,`rows`=?,`params`=?,`groups`=? where moduleId=?';
		$bindvars = array($name,$position,$ord,$title,$cache_time,$rows,$params,$groups,$moduleId);
		$result = $this->query($query, $bindvars);
	}

	function get_workspace_assigned_module($moduleId) {
		$query = "select * from `aulawiki_workspace_modules` where `moduleId`=?";

		$result = $this->query($query,array($moduleId));

		$res = $result->fetchRow();

		return $res;
	}
	
	function get_workspace_assigned_modules($workspaceId,$type) {
		$query = "select * from `aulawiki_workspace_modules` where `workspaceId`=? and `type`=? order by `position` asc,`ord` asc";

		$result = $this->query($query,array($workspaceId,$type));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_workspace_assigned_modules_pos($workspaceId,$type, $pos) {
		$query = "select * from `aulawiki_workspace_modules` where `workspaceId`=? and `type`=? and `position`=? order by `ord` asc";

		$result = $this->query($query,array($workspaceId,$type, $pos));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function workspace_has_assigned_modules($workspaceId,$type) {
		$query = "select count(`name`) from `aulawiki_workspace_modules` where `workspaceId`=? and `type`=?";

		$result = $this->getOne($query,array($workspaceId,$type));
		return $result;
	}

    
    /// Toggle module position
    function move_module($moduleId)
    {
        // Get current position
	    $query = "select `position` from `aulawiki_workspace_modules` where `moduleId`=?";
    	$r = $this->query($query, array($moduleId));
        $res = $r->fetchRow();
        $this->set_column_workspace_module($name, $user, ($res['position'] == 'r' ? 'l' : 'r'));
    }
}

$wsmoduleslib = new WorkspaceModulesLib($dbTiki);

?>