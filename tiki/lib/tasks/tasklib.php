<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

/* Task properties:
   user, taskId, title, description, date, status, priority, completed, percentage
*/
class TaskLib extends TikiLib {
	function TaskLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to TaskLib constructor");
		}
		$this->db = $db;
	}

	function get_task($user, $taskId) {
		$query = "select * from `tiki_user_tasks` where `user`=? and `taskId`=?";
		$result = $this->query($query,array($user,(int)$taskId));
		$res = $result->fetchRow();
		return $res;
	}

	function update_task_percentage($user, $taskId, $perc) {
		$query = "update `tiki_user_tasks` set `percentage`=? where `user`=? and `taskId`=?";
		$this->query($query,array((int)$perc,$user,(int)$taskId));
	}

	function open_task($user, $taskId) {
		$query = "update `tiki_user_tasks` set `completed`=?, `status`=?, `percentage`=? where `user`=? and `taskId`=?";
		$this->query($query, array(0,'o',0,$user,(int)$taskId));
	}
}

$tasklib = new TaskLib($dbTiki);

?>
