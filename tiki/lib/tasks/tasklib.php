<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
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
	
	/**
	 * Rerturn the highst version number 
	 * $belongs_to is the ID of the first task in a history
	 **/
	function get_newest_version_number($belongs_to){
		$query  = "select max(`task_version`) from `tiki_user_tasks` ";
		$query .= "where `belongs_to`= ?"; 
		$newest_version = $this->getOne($query,array($belongs_to)); 
		return $newest_version;
	}
	
	/**
	 * Rerturns the taskID's of a task hostry
	 * $belongs_to is the ID of the first task in a history
	 **/
	function list_taskId_form_history($belongs_to){
		$query  = "select `taskId`, `task_version` from `tiki_user_tasks` ";
		$query .= "where `belongs_to`= ? order by `task_version`" ; 
		$ret = array();
		$result = $this->query($query,array($belongs_to));
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}
	
	function get_task($user, $taskId, $user_check = true) { 
		if($user_check){
			$query  = "select distinct * from `tiki_user_tasks`, `users_users`, `users_usergroups` "; 
			$query .= "where `taskId`= ? and "; 
			$query .= "((`user`=? or `creator` = ? ) or ";
			$query .= "(`users_users`.`login` = ? and "; 
			$query .= "`users_users`.`userId` = `users_usergroups`.`userId`  and "; 
			$query .= "`users_usergroups`.`groupName` = `tiki_user_tasks`.`public_for_group`))"; 
			$value = array((int)$taskId, $user, $user, $user);
		} else {
			$query  = "select distinct * from `tiki_user_tasks` "; 
			$query .= "where `taskId`= ? "; 
			$value = array((int)$taskId);
		} 
		$result = $this->query($query,$value); 
		$res = $result->fetchRow(); 
		$res['newest_version'] = $this->get_newest_version_number($res['belongs_to']);
		return $res; 
    }
	
	function check_right_on_task($user, $task){
		if($task['deleted'] != null) { 
			return 'view';
		} else if($this->get_newest_version_number($task['belongs_to']) != $task['task_version']) { 
			return 'view';
		} else if(($task['user'] == $user) and ($task['creator'] == $user) and ($task['taskId'] == 0)){
			return 'new';
		} else 
		if(($task['user'] == $user) and ($task['creator'] == $user)){
			return 'private';
		} else 
		if(($task['user'] == $user) and ($task['creator'])){
			if($task['rights_by_creator'] == 'y') return 'user';
			else return 'creator';
		} else 
		if(($task['user'] != $user) and ($task['creator'] == $user)){
			return 'creator';
		} else 
		return 'view';
	}
	
	function get_default_new_task($user) {
		$task = array();
		$task['user'] = $user;
		$task['taskId'] = 0;
		$task['belongs_to'] = 0;
		$task['task_version'] = 0;
		$task['title'] = null;
		$task['description'] = null;
		$task['date'] = date("U");
		$task['start'] = null;
		$task['end'] = null;
		$task['status'] = 'o';
		$task['priority'] = 3;
		$task['completed'] = null;
		$task['percentage'] = null;
		$task['lasteditor'] = $user;
		$task['changes'] = date("U");
		$task['deleted'] = null;
		$task['creator'] = $user;
		$task['accepted_creator'] = null;
		$task['accepted_user'] = null;
		$task['public_for_group'] = null;
		$task['rights_by_creator'] = null;
		$task['info'] = null;
		return $task;
	}
	
	function update_task_percentage($user, $taskId, $perc) {
		$query = "update `tiki_user_tasks` set `percentage`=? where (`user`=? or `creator` = ?) and `taskId`=?";
		$this->query($query,array((int)$perc,$user,$user,(int)$taskId));
	}

	function open_task($user, $taskId) {
		$query = "update `tiki_user_tasks` set `completed`=?, `status`=?, `percentage`=? where (`user`=? or `creator` = ?) and `taskId`=?";
		$this->query($query, array(0,'o',0,$user,$user,(int)$taskId));
	}
	
	
	function move_task_into_trash($user, $taskId) {
		$query = "update `tiki_user_tasks` set deleted=? where (`user`=? or `creator` = ?) and `taskId`=?";
		$this->query($query, array(date("U"),$user,$user,(int)$taskId));
	}
	
	function remove_task_from_trash($user, $taskId) {
		$query = "update `tiki_user_tasks` set deleted=? where (`user`=? or `creator` = ?) and `taskId`=?";
		$this->query($query, array(null,$user,$user,(int)$taskId));
	}
	
	
	function accept_task($user, $taskId, $value) {
		$query = "update `tiki_user_tasks` set accepted_user = ? where `user`=? and `taskId`=?";
		$this->query($query, array($value,$user,(int)$taskId));
		$query = "update `tiki_user_tasks` set accepted_creator = ? where `creator`=? and `taskId`=?";
		$this->query($query, array($value,$user,(int)$taskId));
	}
	
	function write_task_in_db(	$user,
								$taskId,
								$belongs_to,
								$task_version,
								$title,
								$description,
								$date,
								$start,
								$end,
								$status,
								$priority,
								$completed,
								$percentage,
								$lasteditor,
								$changes,
								$deleted,
								$creator,
								$accepted_creator,
								$accepted_user,
								$public_for_group,
								$rights_by_creator,
								$info){
		if($belongs_to != 0){
			$query  = "select max(task_version) from `tiki_user_tasks` ";
			$query .= "where `belongs_to`= ?"; 
			$newest_version = $this->getOne($query,array($belongs_to));
			if($task_version <= $newest_version) return -1;
		} 
		
		$query = "insert into `tiki_user_tasks`(	`user`,
													`belongs_to`,
													`task_version`,
													`title`,
													`description`,
													`date`,
													`start`,
													`end`,
													`status`,
													`priority`,
													`completed`,
													`percentage`,
													`lasteditor`,
													`changes`,
													`deleted`,
													`creator`,
													`accepted_creator`, 
													`accepted_user`, 
													`public_for_group`, 
													`rights_by_creator`, 
													`info`) ";
		$query.= " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$this->query($query,array(	$user,
									$belongs_to,
									$task_version,
									$title,
									$description,
									$date,
									$start,
									$end,
									$status,
									$priority,
									$completed,
									$percentage,
									$lasteditor,
									$changes,
									$deleted,
									$creator,
									$accepted_creator,
									$accepted_user,
									$public_for_group,
									$rights_by_creator,
									$info));
		$query = "select  max(`taskId`) from `tiki_user_tasks` where `user` = ? and `title` = ? and `date` = ?";
		$taskId = 	$this->getOne($query, array($user,$title,$date));
		if($belongs_to == 0){
				$query = "update `tiki_user_tasks` set `belongs_to` = ?  where `taskId`=?";
				$this->query($query,array((int)$taskId, (int)$taskId));
		}		
		return $taskId;
	}

    /*shared*/
    function complete_task($user, $taskId) {
        $now = date("U");
        $query = "update `tiki_user_tasks` set `completed`=?, `status`='c', `percentage`=100 where (`user`=? or `creator` = ?) and `taskId`=?";
        $this->query($query,array((int)$now,$user,$user,(int)$taskId));
    }

    /*shared*/
    function remove_task($user, $taskId) {
		$now = date("U");
		$task = $this->get_task($user, $taskId);
		$belongs_to = $task['belongs_to'];
		$query = "update `tiki_user_tasks` set `deleted`=? where (`user`=? or `creator` = ?) and `belongs_to` =?";
		$this->query($query,array((int)$now, $user, $user, (int)$belongs_to));
    }

    /**
	* Retruns the tasklist for a user
	* $user 
	* $offset			
	* $maxRecords 		lenth of the returned list if it is -1 it returns the full list
	* $find 			serach string 
	* $sort_mode 		for sorting, 
	* $show_private 	show private tasks user == creator it shows also shared tasks
	*					to use this option $use_show_shared_for_group must be false 
	* $show_submitted 	show submitted tasks creator == user and user != $user it shows also shared tasks 
	*					to use this option $use_show_shared_for_group must be false 
	* $show_received 	show received tasks creator != user and user == $user it shows also shared tasks 
	*					to use this option $use_show_shared_for_group must be false 
	* $show_shared 		show shared tasks creator != user and user != user 
	*					but the user is in the group to view the this task
	*					to use this option $use_show_shared_for_group must be false 
	* $use_show_shared_for_group
	*					enables the optin $show_shared_for_group with true and 
	*					disables $show_shared, $show_received, $show_submitted and $show_private
	* $show_shared_for_group
	*					shows on null all shared tasks or by a value the tasks to the group value	
	* $show_trash		if on true it shows also the as deleted marked tasks	
	* $show_completed	if on true it shows also the as completed marked tasks	
	* $use_admin_mode	shows all shard tasks also if the user is not in the group to view the task
	* changed by sir-b 18th of January  2005
	**/
    function list_tasks($user, $offset = 0, $maxRecords = -1, $find = null, $sort_mode = 'priority_asc',
						$show_private = true, $show_submitted = true, $show_received = true, $show_shared = true, 
						$use_show_shared_for_group = false, $show_shared_for_group = null, 
						$show_trash = false, $show_completed = false,
						$use_admin_mode = false) {
	
		$values = array();
		if($use_admin_mode){
			$query  = " FROM `tiki_user_tasks` AS `t1` ";
			$query .= "INNER JOIN  `tiki_user_tasks` AS `t2` ON `t1`.`belongs_to` = `t2`.`belongs_to` ";
			$query .= "WHERE `t1`.`public_for_group` IS NOT NULL ";
		} else { 
			$query  = "FROM `tiki_user_tasks` AS `t1` ";
			$query .= "INNER JOIN `tiki_user_tasks` AS `t2` ON `t1`.`belongs_to` = `t2`.`belongs_to`, ";
			$query .= "`users_users`, `users_usergroups` ";
			$query .= "WHERE ";
			$query .= "( ";
			$query .= "( `t1`.`taskId` = -1) "; //Dummy
			if($use_show_shared_for_group){
					$query .= " OR ";
					$query .= "(`users_users`.`login` = ? AND `users_users`.`userId` = `users_usergroups`.`userId` "; 
					$query .= "AND `users_usergroups`.`groupName` = `t1`.`public_for_group` ";
					$values[] = $user;
				if($show_shared_for_group){
					$query .= "AND `t1`.`public_for_group` = ? ";
					$values[] = $show_shared_for_group;
					$query .= ") ";
				}
			} else {
				//private
				if($show_private){
					$query .= " OR ";
					$query .= "(`t1`.`user` = ? AND `t1`.`creator` = ? ) "; 
					$values[] = $user;
					$values[] = $user;
				}
				//submitted
				if($show_submitted){
					$query .= " OR ";
					$query .= "(`t1`.`user` != ? AND `t1`.`creator` = ? ) "; 
					$values[] = $user;
					$values[] = $user;
				}
				//received
				if($show_received){
					$query .= " OR ";
					$query .= "(`t1`.`user` = ? AND `t1`.`creator` != ? ) "; 
					$values[] = $user;
					$values[] = $user;
				}
				//shared
				if($show_shared){
					$query .= " OR ";
					$query .= "(`t1`.`user` != ? AND `t1`.`creator` != ? "; 
					$query .= "AND `users_users`.`login` = ? AND `users_users`.`userId` = `users_usergroups`.`userId` "; 
					$query .= "AND `users_usergroups`.`groupName` = `t1`.`public_for_group`) ";
					$values[] = $user;
					$values[] = $user;
					$values[] = $user;
				}
			}
			$query .= ") ";
			if($find){
				$findesc = "'%" . $find . "%'";
				$query .= " AND ";
				$query .= "( ";
				$query .= "`t1`.`title` like $findesc or ";
				$query .= "`t1`.`description` like $findesc  or";
				$query .= "`t1`.`info` like $findesc or ";
				$query .= "`t1`.`user` like $findesc or ";
				$query .= "`t1`.`creator` like $findesc ";
				$query .= ") ";
			}
			if($show_trash == false){
				$query .= " AND ";
				$query .= "( `t1`.`deleted` IS NULL) ";
			}
			if($show_completed == false){
				$query .= " AND ";
				$query .= "( `t1`.`completed` IS NULL) ";
			}
			
		}
		$query .= "GROUP BY ";
		$query .= "`t1`.`taskID`, ";
		$query .= "`t1`.`belongs_to`, ";
		$query .= "`t1`.`task_version`, ";
		$query .= "`t1`.`title`, ";
		$query .= "`t1`.`description`, ";
		$query .= "`t1`.`user`, ";
		$query .= "`t1`.`creator` ";
		$query .= "HAVING `t1`.`task_version` = MAX(`t2`.`task_version`) ";
		
		if(isset($sort_mode) and strlen($sort_mode) > 1) $order_str = "`t1`.".$this->convert_sortmode($sort_mode) . ", ";
		else $order_str = '';
		$query .= "ORDER BY $order_str `t1`.`taskId` desc";
		
		
		/*
		Currently I am counting over all entries, that can be optimized if somebody knows how to cont 
		the entries with one select
		$query_count = "select count(*) $query";
		$cant = $this->getOne($query_count,$values);
		*/
		$cant = 0;
		
		$tasklist = array();
		
		
		$query_tasklist = "select `t1`.* $query";
		//$result = $this->query($query_tasklist, $values, $maxRecords, $offset);
		//echo("<br>$query_tasklist<br>");
		$result = $this->query($query_tasklist, $values);
		while ($task = $result->fetchRow()) {
			$cant ++;	
			if($cant > $offset and ($maxRecords > 0 or $maxRecords == -1)){
				if($task['user'] == $user or $task['creator'] == $user or $use_admin_mode) $task['disabled'] = false;
				else $task['disabled'] = true;
				$tasklist[] = $task;
				if($maxRecords != -1) $maxRecords--;
			}
		}
		$retval = array();
		$retval["data"] = $tasklist;
		$retval["cant"] = $cant;
		return $retval;
	}

	function emty_trash($user) {
		$query  = "select belongs_to from `tiki_user_tasks` where `creator` = ? and `deleted` IS NOT NULL";
		$result = $this->query($query,array($user));
			while ($res = $result->fetchRow()) {
				$query = "delete from `tiki_user_tasks` where belongs_to=? ";
				$this->query($query,$res);
			}
	}
	
	
	function get_user_with_permissions($perm) {
		/*
		$query = "SELECT DISTINCT `users_users`.`login` AS `login` ";
		$query.= "FROM  `users_grouppermissions`, `users_usergroups`, `users_users` ";
		$query.= "WHERE `users_usergroups`.`userId` = `users_users`.`userId` AND ";
		$query.= "`users_grouppermissions`.`groupName` = `users_usergroups`.`groupName` AND ";
		$query.= "`users_grouppermissions`.`permName` = ? ";
		$query.= "ORDER BY `login`";
		$result = $this->query($query, array($perm));
		*/
		$query = "SELECT DISTINCT `users_users`.`login` AS `login` FROM `users_users`";
		$result = $this->query($query, array());
		$ret = array();
		while ($res = $result->fetchRow()) {
				$ret[] = $res;
		}
	    return $ret;
	}
	
}

$tasklib = new TaskLib($dbTiki);

?>
