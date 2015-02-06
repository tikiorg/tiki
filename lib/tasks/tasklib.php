<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}


/* Task properties:
   user, taskId, title, description, date, status, priority, completed, percentage
*/
class TaskLib extends TikiLib
{

	public function get_task($user, $taskId, $task_version = null, $admin_mode = false)
	{
		if ($admin_mode) {
			$query  = "select distinct `t_head`.*, `t_history`.* FROM ";
			$query .= "`tiki_user_tasks_history` AS `t_history`, `tiki_user_tasks` AS `t_head` ";
			$query .= "WHERE ";
			$query .= "`t_head`.`taskId` = `t_history`.`belongs_to` AND";
			if ($task_version == null) {
				$query .= "`t_history`.`task_version` = `t_head`.`last_version` AND ";
			} else {
				$query .= "`t_history`.`task_version` = $task_version AND ";
			}
			$query .= "`t_head`.`taskId`= ? ";
			$value = array((int) $taskId);
		} else {
			$query  = "select distinct `t_head`.*, `t_history`.* FROM ";
			$query .= "`tiki_user_tasks_history` AS `t_history`, `tiki_user_tasks` AS `t_head`, ";
			$query .= "`users_users`, `users_usergroups` ";
			$query .= "WHERE ";
			$query .= "`t_head`.`taskId` = `t_history`.`belongs_to` AND";
			if ($task_version == null) {
				$query .= "`t_history`.`task_version` = `t_head`.`last_version` AND ";
			} else {
				$query .= "`t_history`.`task_version` = $task_version AND ";
			}
			$query .= "`t_head`.`taskId`= ? AND ";
			$query .= "((`t_head`.`user`=? or `t_head`.`creator` = ? ) or ";
			$query .= "(`users_users`.`login` = ? and ";
			$query .= "`users_users`.`userId` = `users_usergroups`.`userId`  and ";
			$query .= "`users_usergroups`.`groupName` = `t_head`.`public_for_group`))";
			$value = array((int) $taskId, $user, $user, $user);
		}

		$result = $this->query($query, $value);
		if ($res = $result->fetchRow()) {
			if ( ($res['user'] == $user and  $res['rights_by_creator'] == null) or ($res['creator'] == $user)) {
				$res['disabled'] = false;
			} else {
				$res['disabled'] = true;
			}
			if ($res['percentage'] == null) {
				$res['percentage_null'] = true;
			} else {
				$res['percentage_null'] = false;
			}
		}
		return $res;
	}

	public function get_default_new_task($user)
	{
		$task = array();
		$task['taskId'] = 0;
		$task['task_version'] = 0;
		$task['user'] = $user;
		$task['creator'] = $user;
		$task['public_for_group'] = null;
		$task['rights_by_creator'] = 'y';
		$task['priority'] = 3;
		$task['completed'] = null;
		$task['deleted'] = null;
		$task['created'] = $this->now;
		$task['status'] = null;
		$task['percentage'] = null;
		$task['accepted_creator'] = null;
		$task['accepted_user'] = null;
		/*--history --*/
		$task['belongs_to'] = 0;
		$task['last_version'] = 0;
		$task['title'] = null;
		$task['description'] = null;
		$task['start'] = null;
		$task['end'] =null;
		$task['lasteditor'] = $user;
		$task['lastchanges'] = $this->now;
		/*--*/
		$task['percentage_null'] = true;

		return $task;
	}

	public function accept_task($user, $taskId, $value)
	{
		$query = "update `tiki_user_tasks` set accepted_user = ? where `user`=? and `taskId`=?";
		$this->query($query, array($value,$user,(int) $taskId));
		$query = "update `tiki_user_tasks` set accepted_creator = ? where `creator`=? and `taskId`=?";
		$this->query($query, array($value,$user,(int) $taskId));
	}


	public function new_task($task_user, $creator, $public_for_group, $rights_by_creator, $created, $values)
	{
		$query  = "INSERT INTO `tiki_user_tasks` ( ";
		$query .= "`last_version`, `user`, `creator`, ";
		$query .= "`public_for_group`, `rights_by_creator`, `created`) ";
		$query .= "VALUES (?, ?, ?, ?, ?, ?)";
		$this->query($query, array((int) 0, $task_user, $creator, $public_for_group, $rights_by_creator, (int) $created));
		$query = "select `taskId` from `tiki_user_tasks` where `creator` = ? AND `created` = ?";
		$taskId = $this->getOne($query, array($creator, (int) $created));
		$values['belongs_to'] = $taskId;
		$values['lasteditor'] = $creator;
		$values['lastchanges'] = $created;
		$values['task_version'] = (int) 0;
		if ($task_user != $creator) {
			$values['accepted_creator'] = 'y';
		}
		$query  = "INSERT INTO `tiki_user_tasks_history` ( ";
		$comma = '';
		$query_values = "";
		foreach ($values as $key => $value) {
			$query .= "$comma `$key`";
			$query_values .= "$comma ?";
			$comma = ', ';
		}
		$query .= " ) VALUES ( " . $query_values . ")";
		$this->query($query, array_values($values));
		return $taskId;
	}

	public function update_task($taskId, $user, $values, $values_head = null, $admin_mode = false)
	{
		$query  = "SELECT `tiki_user_tasks_history`.* ";
		$query .= "FROM `tiki_user_tasks`, `tiki_user_tasks_history` WHERE ";
		$query .= "`tiki_user_tasks`.`taskId` = ? AND ";
		$query .= "`tiki_user_tasks`.`taskId` = `tiki_user_tasks_history`.`belongs_to` AND ";
		$query .= "`tiki_user_tasks`.`last_version` = `tiki_user_tasks_history`.`task_version` ";
		$values_select = array((int) $taskId);
		if (!$admin_mode) {
			$values_select[] = $user;
			$values_select[] = $user;
			$query .= " AND (`user` = ?  OR `creator` = ?) ";
		}
		$result = $this->query($query, $values_select);
		$entries = $result->fetchRow();
		// not needed anymore ? // Sept
		for ($index=0; array_key_exists($index, $entries); $index++) {
			// Hack since PDO fetchRow returns 2 indexes per DB field
			unset($entries[$index]);
		}

		$query  = "INSERT INTO `tiki_user_tasks_history` (";
		$query_values = ") VALUES (";

		$count_values = 0;
		foreach ($values as $key => $value) {
			$entries[$key] =  $value;
			$count_values++;
			//echo("$key: $value<br />");
		}

		if ($entries['percentage'] == null) {
			$entries['status'] = null;
			$entries['completed'] = null;
		} else if ($entries['percentage'] >= 100) {
			$entries['status'] = 'c';
			$entries['completed'] = $this->now;
		} else {
			$entries['status'] = 'o';
			$entries['completed'] = null;
		}


		if ($count_values > 0) {
			$count_entries = 0;
			$entries['task_version'] = $entries['task_version'] + 1;
			$entries['lasteditor'] = $user;
			$entries['lastchanges'] = $this->now;
			$comma = '';
			foreach ($entries as $key => $value) {
				$query .= "$comma `$key`";
				$query_values.= "$comma ?";
				$comma  = ', ';
				$count_entries++;
				//echo("entries.$key: $value<br />");
			}
			//echo("$query<br />");
			$query .= $query_values . ")";
			$this->query($query, array_values($entries));
		}

		$insert_values = array();
        $query  = "UPDATE `tiki_user_tasks` SET `last_version`= ? ";
		$insert_values['last_version'] = (int) $entries['task_version'];
		$count_values_head = 0;
		if ($values_head != null) {
			foreach ($values_head as $key => $value) {
				$query .= ", `$key` = ? ";
				$insert_values[$key] =  $value;
				$count_values_head++;
			}
		}
		$insert_values['taskId'] = (int) $taskId;
		$query .= "WHERE `taskId`=? ";
		if ($count_values > 0 or $count_values_head > 0) {
			$this->query($query, array_values($insert_values));
		}
		return $taskId;
	}

	public function mark_task_as_trash($taskId, $user, $admin_mode = false)
	{
		$result = $this->query('SELECT * FROM `tiki_user_tasks` WHERE taskId = ?', array($taskId));
		$res = $result->fetchRow();
		if ($user == $res['creator'] or  ($user == $res['user'] and $res['rights_by_creator'] == null) or $admin_mode) {
			$values = array('deleted' => (int) $this->now);
			$this->update_task($taskId, $user, $values, null, $admin_mode);
		}
	}

	public function unmark_task_as_trash($taskId, $user, $admin_mode = false)
	{
		$result = $this->query("SELECT * FROM `tiki_user_tasks` WHERE taskId = ?", array($taskId));
		$res = $result->fetchRow();
		if ($user == $res['creator'] or  ($user == $res['user'] and $res['rights_by_creator'] == null) or $admin_mode) {
			$values = array('deleted' => null);
			$this->update_task($taskId, $user, $values, null, $admin_mode);
		}
	}

	public function open_task($taskId, $user)
	{
		$values = array('percentage' => (int) 0, 'status' => 'o', 'completed' => null);
		$this->update_task($taskId, $user, $values);
	}

	public function waiting_task($taskId, $user)
	{
		$values = array('percentage' => null, 'status' => null, 'completed' => null);
		$this->update_task($taskId, $user, $values);
	}

	public function mark_complete_task($taskId, $user)
	{
		$values = array('percentage' => (int) 100, 'status' => 'c', 'completed' => (int) $this->now);
		$this->update_task($taskId, $user, $values);
	}

	public function update_task_percentage($taskId, $user, $percentage)
	{
		$values = array('percentage' => $percentage);
		$this->update_task($taskId, $user, $values);
	}

	public function emty_trash($user)
	{
		$query  = "SELECT  `tiki_user_tasks`.`taskId` FROM `tiki_user_tasks`, `tiki_user_tasks_history` ";
		$query .= "WHERE `tiki_user_tasks`.`creator` = ? AND ";
		$query .= "`tiki_user_tasks`.`taskId` = `tiki_user_tasks_history`.`belongs_to` AND ";
		$query .= "`tiki_user_tasks`.`last_version` = `tiki_user_tasks_history`.`task_version` AND ";
		$query .= "`tiki_user_tasks_history`.`deleted` IS NOT NULL";
		$result = $this->query($query, array($user));
		while ($res = $result->fetchRow()) {
			$query = "DELETE FROM `tiki_user_tasks_history` WHERE `belongs_to` = ?";
			$this->query($query, $res['taskId']);
			$query  = "DELETE FROM `tiki_user_tasks` WHERE `taskId` = ?";
			$this->query($query, $res['taskId']);
		}
	}

    /**
	* Returns the tasklist for a user
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
	**/
	public function list_tasks($user, $offset = 0, $maxRecords = -1, $find = null
		, $sort_mode = 'priority_asc', $show_private = true
		, $show_submitted = true, $show_received = true
		, $show_shared = true, $use_show_shared_for_group = false
		, $show_shared_for_group = null, $show_trash = false
		, $show_completed = false, $use_admin_mode = false
	)
	{
		$list_tasks_start = microtime();
		$values = array();
		if ($use_admin_mode) {
			$query  = "FROM `tiki_user_tasks_history` AS `t_history`, `tiki_user_tasks` AS `t_head` ";
			$query .= "WHERE `t_head`.`public_for_group` IS NOT NULL ";
			$query .= "AND `t_head`.`taskId` = `t_history`.`belongs_to` ";
			$query .= "AND `t_head`.`last_version` = `t_history`.`task_version` ";
		} else {
			$query  = "FROM `tiki_user_tasks_history` AS `t_history`, `tiki_user_tasks` AS `t_head` ";
			if ($show_shared or $use_show_shared_for_group) {
				$userid = $this->get_user_id($user);
				$query .= ", `users_usergroups` ";
			}
			$query .= "WHERE ";
			$query .= "`t_head`.`taskId` = `t_history`.`belongs_to` AND ";
			$query .= "`t_head`.`last_version` = `t_history`.`task_version` AND ";
			$query .= "( ";
			$query .= "( 1 = 0 ) "; //Dummy
			if ($use_show_shared_for_group) {
					$query .= " OR ";
					$query .= "(`users_usergroups`.`userId` = ?";
					$query .= "AND `users_usergroups`.`groupName` = `t_head`.`public_for_group` ";
					$values[] = $userid;
				if ($show_shared_for_group) {
					$query .= "AND `t_head`.`public_for_group` = ? ";
					$values[] = $show_shared_for_group;
					$query .= ") ";
				}
			} else {
				//private
				if ($show_private) {
					$query .= " OR ";
					$query .= "(`t_head`.`user` = ? AND `t_head`.`creator` = ? ) ";
					$values[] = $user;
					$values[] = $user;
				}
				//submitted
				if ($show_submitted) {
					$query .= " OR ";
					$query .= "(`t_head`.`user` != ? AND `t_head`.`creator` = ? ) ";
					$values[] = $user;
					$values[] = $user;
				}
				//received
				if ($show_received) {
					$query .= " OR ";
					$query .= "(`t_head`.`user` = ? AND `t_head`.`creator` != ? ) ";
					$values[] = $user;
					$values[] = $user;
				}
				//shared
				if ($show_shared) {
					$query .= " OR ";
					$query .= "(`t_head`.`user` != ? AND `t_head`.`creator` != ? ";
					$query .= "AND `users_usergroups`.`userId` = ? ";
					$query .= "AND `users_usergroups`.`groupName` = `t_head`.`public_for_group`) ";
					$values[] = $user;
					$values[] = $user;
					$values[] = $userid;
				}
			}
			$query .= ") ";
			if ($find) {
				$query .= " AND ";
				$query .= "( ";
				$query .= "`t_history`.`title` like ? or ";
				$query .= "`t_history`.`description` like ? or";
				$query .= "`t_head`.`user` like ? or ";
				$query .= "`t_head`.`creator` like ? ";
				$query .= ") ";
				$values[] = "%" . $find . "%";
				$values[] = "%" . $find . "%";
				$values[] = "%" . $find . "%";
				$values[] = "%" . $find . "%";
			}
			if ($show_trash == false) {
				$query .= " AND ";
				$query .= "( `t_history`.`deleted` IS NULL) ";
			}
			if ($show_completed == false) {
				$query .= " AND ";
				$query .= "( `t_history`.`completed` IS NULL) ";
			}

		}
		if (isset($sort_mode) and strlen($sort_mode) > 1) {
			$order_str = "`t_history`.".$this->convertSortMode($sort_mode) . ", ";
		} else {
			$order_str = '';
		}

		// Place the count query before the addition of the order by
		// clause to make the query work in postgres..
		$query_count = "select count(distinct `t_head`.`taskId`) $query";
		$cant = $this->getOne($query_count, $values);

		$query .= "ORDER BY $order_str `t_head`.`taskId` desc";

		$tasklist = array();

		$query_tasklist = "select distinct `t_head`.*, `t_history`.* $query";
		$result = $this->query($query_tasklist, $values, $maxRecords, $offset);
		//echo("$query_tasklist<br />");
		while ($res = $result->fetchRow()) {
			if (($res['user'] == $user) or ($res['creator'] == $user)) {
				$res['disabled'] = false;
			} else {
				$res['disabled'] = true;
			}
			if ($res['percentage'] == null) {
				$res['percentage_null'] = true;
			} else {
				$res['percentage_null'] = false;
			}
			$tasklist[] = $res;
		}
		$retval = array();
		$retval["data"] = $tasklist;
		$retval["cant"] = $cant;

		$list_tasks_end = microtime();
		$list_tasks_time = $list_tasks_end - $list_tasks_start;
		return $retval;
	}

	public function get_user_with_permissions($perm)
	{
		$query = "SELECT DISTINCT `users_users`.`login` AS `login` FROM `users_users` ORDER BY `login`";
		$result = $this->query($query, array());
		$ret = array();
		while ($res = $result->fetchRow()) {
				$ret[] = $res;
		}
		sort($ret);
	    return $ret;
	}

}
$tasklib = new TaskLib;
