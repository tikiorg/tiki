<?php

class UserModulesLib extends TikiLib {
	function UserModulesLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to UserModulesLib constructor");
		}

		$this->db = $db;
	}

	function unassign_user_module($name, $user) {
		$query = "delete from tiki_user_assigned_modules where name='$name' and user='$user'";

		$result = $this->query($query);
	}

	function up_user_module($name, $user) {
		$query = "update tiki_user_assigned_modules set ord=ord-1 where name='$name' and user='$user'";

		$result = $this->query($query);
	}

	function down_user_module($name, $user) {
		$query = "update tiki_user_assigned_modules set ord=ord+1 where name='$name' and user='$user'";

		$result = $this->query($query);
	}

	function set_column_user_module($name, $user, $position) {
		$query = "update tiki_user_assigned_modules set position='$position' where name='$name' and user='$user'";

		$result = $this->query($query);
	}

	function assign_user_module($module, $position, $order, $user) {
		$query = "select * from tiki_modules where name='$module'";

		$result = $this->query($query);
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$query2 = '
    	REPLACE INTO
    		tiki_user_assigned_modules
    	(
    		user,
    		name,
    		position,
    		ord,
    		type,
    		title,
    		cache_time,
    		rows,
    		groups
    	) VALUES
    		(?,?,?,?,?,?,?,?,?)
    ';
		$fields = array(
			$user,
			$module,
			$position,
			$order,
			$res['type'],
			$res['title'],
			$res['cache_time'],
			$res['rows'],
			$res['groups'],
		);

		$result2 = $this->query($query2, $fields);
	}

	function get_user_assigned_modules($user) {
		$query = "select * from tiki_user_assigned_modules where user='$user' order by position asc,ord asc";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_user_assigned_modules_pos($user, $pos) {
		$query = "select * from tiki_user_assigned_modules where user='$user' and position='$pos' order by ord asc";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_assigned_modules_user($user, $position) {
		$query = "select * from tiki_user_assigned_modules where user='$user' and position='$position' order by ord asc";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		return $ret;
	}

	function user_has_assigned_modules($user) {
		$query = "select name from tiki_user_assigned_modules where user='$user'";

		$result = $this->query($query);
		return $result->numRows();
	}

	// Creates user assigned modules copying from tiki_modules
	function create_user_assigned_modules($user) {
		$query = "delete from tiki_user_assigned_modules where user='$user'";

		$result = $this->query($query);
		global $modallgroups;
		$query = "select * from tiki_modules";
		$result = $this->query($query);
		$ret = array();
		$user_groups = $this->get_user_groups($user);

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$mod_ok = 0;

			if ($res["groups"] && $modallgroups != 'y') {
				$groups = unserialize($res["groups"]);

				$ins = array_intersect($groups, $user_groups);

				if (count($ins) > 0) {
					$mod_ok = 1;
				}
			} else {
				$mod_ok = 1;
			}

			if ($mod_ok) {
				$query2 = "
			REPLACE INTO
				tiki_user_assigned_modules
			(
				user,
				name,
				position,
				ord,
				type,
				title,
				cache_time,
				rows,
				groups,
				params
			) VALUES (
				?,?,?,?,?,?,?,?,?,?
			)
		";

				$fields = array(
					$user,
					$res['name'],
					$res['position'],
					$res['ord'],
					$res['type'],
					$res['title'],
					$res['cache_time'],
					$res['rows'],
					$res['groups'],
					$res['params'],
				);

				$result2 = $this->query($query2, $fields);
			}
		}
	}

	// Return the list of modules that CAN be assigned by the user (he may have assigned or not the modules)
	function get_user_assignable_modules($user) {
		global $modallgroups;

		$query = "select * from tiki_modules";
		$result = $this->query($query);
		$ret = array();
		$user_groups = $this->get_user_groups($user);

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$mod_ok = 0;

			// The module must not be assigned
			$isas = $this->getOne(
				"select count(*) from tiki_user_assigned_modules where name='" . $res["name"] . "' and user='" . $user . "'");

			if (!$isas) {
				if ($res["groups"] && $modallgroups != 'y' && $user != 'admin') {
					$groups = unserialize($res["groups"]);

					$ins = array_intersect($groups, $user_groups);

					if (count($ins) > 0) {
						$mod_ok = 1;
					}
				} else {
					$mod_ok = 1;
				}

				if ($mod_ok) {
					$ret[] = $res;
				}
			}
		}

		return $ret;
	}
}

$usermoduleslib = new UserModulesLib($dbTiki);

?>