<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/** \file
 * \brief Manage user assigned modules
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * \brief Class to manage user assigned modules
 *
 * Useful only if the feature "A user can assign modules has been set" ($prefs['user_assigned_modules'])
 *
 * The first time, a user displays the page to assign modules(tiki-user_assigned_modules.php), 
 * the list of modules are copied from tiki_modules to tiki_user_assigned_modules
 * This list is rebuilt if the user asks for a "restore default"
 *
 */
class UserModulesLib extends TikiLib
{
	function unassign_user_module($moduleId, $user) {
		$query = "delete from `tiki_user_assigned_modules` where `moduleId`=? and `user`=?";
		$result = $this->query($query,array($moduleId, $user));
	}

	function up_user_module($moduleId, $user) {
		$query = "update `tiki_user_assigned_modules` set `ord`=`ord`-1 where `moduleId`=? and `user`=?";
		$result = $this->query($query,array($moduleId, $user));
	}

	function down_user_module($moduleId, $user) {
		$query = "update `tiki_user_assigned_modules` set `ord`=`ord`+1 where `moduleId`=? and `user`=?";
		$result = $this->query($query,array($moduleId, $user));
	}

	function set_column_user_module($moduleId, $user, $position) {
		$query = "update `tiki_user_assigned_modules` set `position`=? where `moduleId`=? and `user`=?";
		$result = $this->query($query,array($position, $moduleId, $user));
	}

	function assign_user_module($moduleId, $position, $order, $user) {
		$query = "select * from `tiki_modules` where `moduleId`=?";
		$result = $this->query($query,array($moduleId));
		$res = $result->fetchRow();
		$query="delete from `tiki_user_assigned_modules` where `moduleId`=? and `user`=?";
		$result=$this->query($query,array($moduleId,$user));
		$query = 'insert into `tiki_user_assigned_modules`(`moduleId`, `user`,`name`,`position`,`ord`,`type`) values(?,?,?,?,?,?)';
		$bindvars = array($moduleId, $user,$res['name'],$position,(int) $order,$res['type']);
		$result = $this->query($query, $bindvars);
	}

	function get_user_assigned_modules($user) {
		$query = "select * from `tiki_user_assigned_modules` where `user`=? order by `position` asc,`ord` asc";

		$result = $this->query($query,array($user));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_user_assigned_modules_pos($user, $pos) {
		$query = "select * from `tiki_user_assigned_modules` where `user`=? and `position`=? order by `ord` asc";

		$result = $this->query($query,array($user, $pos));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_assigned_modules_user($user, $position) {
		$query = "select `umod`.`name`, `umod`.`position`, `umod`.`ord`, `umod`.`type`,
                  `mod`.`title`, `mod`.`cache_time`, `mod`.`rows`, `mod`.`params`,
                  `mod`.`groups`, `umod`.`user`, `mod`.`moduleId`
                  from `tiki_user_assigned_modules` `umod`, `tiki_modules` `mod`
                  where `umod`.`moduleId`=`mod`.`moduleId` and `umod`.`user`=? and `umod`.`position`=? order by `umod`.`ord` asc";

		$result = $this->query($query,array($user, $position));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function user_has_assigned_modules($user) {
		$query = "select count(`moduleId`) from `tiki_user_assigned_modules` where `user`=?";

		$result = $this->getOne($query,array($user));
		return $result;
	}

	// Creates user assigned modules copying from tiki_modules
	function create_user_assigned_modules($user) {
		$query = "delete from `tiki_user_assigned_modules` where `user`=?";

		$result = $this->query($query,array($user));
		global $prefs;
		$query = "select * from `tiki_modules`";
		$result = $this->query($query,array());
		$ret = array();
		$user_groups = $this->get_user_groups($user);

		while ($res = $result->fetchRow()) {
			$mod_ok = 0;
			if ($res['type'] != "h") {
				if ($res["groups"] && $prefs['modallgroups'] != 'y') {
					$groups = unserialize($res["groups"]);

					$ins = array_intersect($groups, $user_groups);

					if (count($ins) > 0) {
						$mod_ok = 1;
					}
				} else {
					$mod_ok = 1;
				}
			}

			if ($mod_ok) {
				$query="delete from `tiki_user_assigned_modules` where `moduleId`=? and `user`=?";
				$result2=$this->query($query,array($res['moduleId'],$user));

				$query = "insert into `tiki_user_assigned_modules`
				(`moduleId`, `user`,`name`,`position`,`ord`,`type`) values(?,?,?,?,?,?)";
				$bindvars = array($res['moduleId'], $user,$res['name'],$res['position'],$res['ord'],$res['type']);
				$result2 = $this->query($query, $bindvars);
			}
		}
	}
	// Return the list of modules that can be assigned by the user
	function get_user_assignable_modules($user) {
		global $prefs,$userlib;

		$query = "select * from `tiki_modules`";
		$result = $this->query($query,array());
		$ret = array();
		$user_groups = $this->get_user_groups($user);

		while ($res = $result->fetchRow()) {
			$mod_ok = 0;

			// The module must not be assigned
			$isas = $this->getOne("select count(*) from `tiki_user_assigned_modules` where `moduleId`=? and `user`=?",array($res['moduleId'],$user));

			if (!$isas) {
				if ($res["groups"] && $prefs['modallgroups'] != 'y' && (!$userlib->user_has_permission($user,'tiki_p_admin'))) {
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
	function swap_up_user_module($moduleId, $user)
    {
        $this->swap_adjacent($moduleId, $user, '<');
	}
    /// Swap current module and below one
	function swap_down_user_module($moduleId, $user)
    {
        $this->swap_adjacent($moduleId, $user, '>');
    }
    /// Function to swap (up/down) two adjacent modules
    function swap_adjacent($moduleId, $user, $op)
    {
        // Get position and order of module to swap
	    $query = "select `ord`,`position` from `tiki_user_assigned_modules` where `moduleId`=? and user=?";
    	$r = $this->query($query, array($moduleId, $user));
        $cur = $r->fetchRow();
        // Get name and order of module to swap with
	    $query = "select `moduleId`, `name`,`ord` from `tiki_user_assigned_modules` where `position`=? and `ord`".$op."=? and `user`=? and `moduleId` != ? order by `ord` ".($op == '<' ? 'desc' : '');
        $r = $this->query($query, array($cur['position'], $cur['ord'], $user, $moduleId));
        $swap = $r->fetchRow();
        if (!empty($swap))
        {
            // Swap 2 adjacent modules
			if ($swap['ord'] == $cur['ord'])
				$swap['ord'] += ($op == '<')? -1:+1;
            $query = "update `tiki_user_assigned_modules` set `ord`=? where `moduleId`=? and `user`=?";
  	        $this->query($query, array($swap['ord'], $moduleId, $user));
            $query = "update `tiki_user_assigned_modules` set `ord`=? where `moduleId`=? and `user`=?";
  	        $this->query($query, array($cur['ord'], $swap['moduleId'], $user));
        }
 	}
    /// Toggle module position
    function move_module($moduleId, $user)
    {
        // Get current position
	    $query = "select `position` from `tiki_user_assigned_modules` where `moduleId`=? and `user`=?";
    	$r = $this->query($query, array($moduleId, $user));
        $res = $r->fetchRow();
        $this->set_column_user_module($moduleId, $user, ($res['position'] == 'r' ? 'l' : 'r'));
    }
	/// Add a module to all the user who have assigned module and who don't have already this module
	function add_module_users($moduleId, $name,$title,$position,$order,$cache_time,$rows,$groups,$params,$type) {
		// for the user who already has this module, update only the type
		$this->query("update `tiki_user_assigned_modules` set `type`=? where `moduleId`=?",array($type,$name)) ;
		// for the user who doesn't have this module
		$query = "select distinct t1.`user` from `tiki_user_assigned_modules` as t1 left join `tiki_user_assigned_modules` as t2 on t1.`user`=t2.`user` and t2.`moduleId`=? where t2.`moduleId` is null";   
		$result = $this->query($query,array($moduleId));
		while ($res = $result->fetchRow()) {
 			$user = $res["user"];
			$query = "insert into `tiki_user_assigned_modules`(`moduleId`, `user`,`name`,`position`,`ord`,`type`)
			values(?,?,?,?,?,?)";
 			$this->query($query,array($moduleId, $user,$name,$position,(int) $order,$type));
		}
	} 
}
$usermoduleslib = new UserModulesLib;
