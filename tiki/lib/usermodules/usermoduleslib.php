<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/lib/usermodules/usermoduleslib.php,v 1.13 2003-08-14 00:33:22 zaufi Exp $
 *
 * \brief Manage user assigned modules
 */

/**
 * \brief Class to manage user assigned modules
 *
 * Useful only if the feature "A user can assign modules has been set" ($user_assigned_modules)
 *
 * The first time, a user displays the page to assign modules(tiki-user_assigned_modules.php), 
 * the list of modules are copied from tiki_modules to tiki_user_assigned_modules
 * This list is rebuilt if the user asks for a "restore default"
 *
 */
class UserModulesLib extends TikiLib {
	function UserModulesLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to UserModulesLib constructor");
		}

		$this->db = $db;
	}

	function unassign_user_module($name, $user) {
		$query = "delete from `tiki_user_assigned_modules` where `name`='$name' and user='$user'";

		$result = $this->query($query);
	}

	function up_user_module($name, $user) {
		$query = "update `tiki_user_assigned_modules` set `ord`=ord-1 where `name`='$name' and user='$user'";

		$result = $this->query($query);
	}

	function down_user_module($name, $user) {
		$query = "update `tiki_user_assigned_modules` set `ord`=ord+1 where `name`='$name' and user='$user'";

		$result = $this->query($query);
	}

	function set_column_user_module($name, $user, $position) {
		$query = "update `tiki_user_assigned_modules` set `position`='$position' where `name`='$name' and user='$user'";

		$result = $this->query($query);
	}

	function assign_user_module($module, $position, $order, $user) {
		$query = "select * from `tiki_modules` where `name`='$module'";

		$result = $this->query($query);
		$res = $result->fetchRow();
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
		$query = "select * from `tiki_user_assigned_modules` where `user`='$user' order by position asc,ord asc";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_user_assigned_modules_pos($user, $pos) {
		$query = "select * from `tiki_user_assigned_modules` where `user`='$user' and position='$pos' order by ord asc";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_assigned_modules_user($user, $position) {
		$query = "select * from `tiki_user_assigned_modules` where `user`='$user' and position='$position' order by ord asc";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function user_has_assigned_modules($user) {
		$query = "select `name` from `tiki_user_assigned_modules` where `user`='$user'";

		$result = $this->query($query);
		return $result->numRows();
	}

	// Creates user assigned modules copying from tiki_modules
	function create_user_assigned_modules($user) {
		$query = "delete from `tiki_user_assigned_modules` where `user`='$user'";

		$result = $this->query($query);
		global $modallgroups;
		$query = "select * from tiki_modules";
		$result = $this->query($query);
		$ret = array();
		$user_groups = $this->get_user_groups($user);

		while ($res = $result->fetchRow()) {
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

		while ($res = $result->fetchRow()) {
			$mod_ok = 0;

			// The module must not be assigned
			$isas = $this->getOne(
				"select count(*) from `tiki_user_assigned_modules` where `name`='" . $res["name"] . "' and user='" . $user . "'");

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
    /// Swap current module and above one
	function swap_up_user_module($name, $user)
    {
        $this->swap_adjacent($name, $user, '<');
	}
    /// Swap current module and below one
	function swap_down_user_module($name, $user)
    {
        $this->swap_adjacent($name, $user, '>');
    }
    /// Function to swap (up/down) two adjacent modules
    function swap_adjacent($name, $user, $op)
    {
        // Get position and order of module to swap
	    $query = "select `ord`,`position` from `tiki_user_assigned_modules` where `name`=? and user=?";
    	$r = $this->query($query, array($name, $user));
        $cur = $r->fetchRow();
        // Get name and order of module to swap with
	    $query = "select `name`,`ord` from `tiki_user_assigned_modules` where `position`=? and ord".$op."? and user=? order by ord ".($op == '<' ? 'desc' : '');
        $r = $this->query($query, array($cur['position'], $cur['ord'], $user));
        $swap = $r->fetchRow();
        if (!empty($swap))
        {
            // Swap 2 adjacent modules
            $query = "update `tiki_user_assigned_modules` set `ord`=? where `name`=? and user=?";
  	        $r = $this->query($query, array($swap['ord'], $name, $user));
            $query = "update `tiki_user_assigned_modules` set `ord`=? where `name`=? and user=?";
  	        $r = $this->query($query, array($cur['ord'], $swap['name'], $user));
        }
 	}
	/// Add a module to all the user who have assigned module and who don't have already this module
	function add_module_users($name,$title,$position,$order,$cache_time,$rows,$groups,$params,$type) {
		// for the user who already has this module, update only the type
		$this->query = "update tiki_user_assigned_modules set type='$type' where name='$name'" ;
		// for the user who doesn't have this module
		$query = "select distinct t1.user from tiki_user_assigned_modules as t1 left join tiki_user_assigned_modules as t2 on t1.user=t2.user and t2.name='$name' where t2.name is null";   
		$result = $this->query($query);
		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
 			$user = $res["user"];
			$query = "insert into tiki_user_assigned_modules(user,name,position,ord,type,title,cache_time,rows,groups)
			values('$user','$name','$position','$order','$type','$title','$cache_time','$rows','$groups')";
 			$this->query($query);
		}
	} 
}

$usermoduleslib = new UserModulesLib($dbTiki);

?>
