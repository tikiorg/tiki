<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/projects/projectlib.php,v 1.2 2005-01-22 22:55:50 mose Exp $

// Damian Parker aka Damosoft

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


class ProjectsLib extends TikiLib {

	function ProjectsLib($db) {
		if(!$db) {
			die("Invalid db object passed to ProjectsLib constructor");
		}
		$this->db = $db;
	}

	function project_exists ($projectName) {
		static $rv = array();

		if (!isset($rv[$projectName])) {
			$query = "select count(`projectName`) from `tiki_projects` where `projectName` = ?";
			$result = $this->getOne($query, array($projectName));
			$rv[$projectName] = $result;
		}

		return $rv[$projectName];
	}

	function add_new_project($projectName, $projectDescription) {
		global $feature_project_group_prefix, $feature_project_group_prefix_admin, $userlib, $tiki_p_project_approves, $user;

		if ($userlib->group_exists($feature_project_group_prefix.$projectName)) {
			$smarty->assign('msg', 'A group $feature_project_group_prefix.$projectName already exists.');
			$smarty->display("error.tpl");
			die;
		}

		if ($userlib->group_exists($feature_project_group_prefix_admin.$projectName)) {
                        $smarty->assign('msg', 'A group $feature_project_group_prefix_admin.$projectName already exists.');
                        $smarty->display("error.tpl");
                        die;
                }

		$now = date("U");

	// Insert Project
		if ($tiki_p_project_approves != 'y') {
			$autoapp = 'n';
		} else {
			$autoapp = 'y';
		}
		$query = "insert into `tiki_projects` (`projectName`, `projectDescription`, `active`, `Created`) Values (?, ?, ?, ?)";
		$result = $this->query($query, array($projectName, $projectDescription, $autoapp, $now));
	
	// Create groups
		$userlib->add_group($feature_project_group_prefix.$projectName, tra("Project Group for ").$projectName, '','','');
		$userlib->add_group($feature_project_group_prefix_admin.$projectName, tra("Project Admin Group for ").$projectName, '','','');
		$userlib->group_inclusion($feature_project_group_prefix_admin.$projectName, $feature_project_group_prefix.$projectName);

	// Add user to admin group
		$userlib->assign_user_to_group($user, $feature_project_group_prefix_admin.$projectName);

	}

	function list_projects($offset = 0, $maxRecords = -1, $sort_mode = 'projectName_asc', $find = '') {
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`projectName` like ? or `projectDescription` like ?) and `active` = ? ";
			$bindvars=array($findesc,$findesc, 'y');
		} else {
			$mid = ' where `active` = ? ';
			$bindvars=array('y');
		}

		$query = "select `projectId`, `projectName`, `projectDescription`, `Created` from `tiki_projects` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(`projectId`) from `tiki_projects` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$add = TRUE;
			global $feature_categories, $userlib, $user, $tiki_p_admin;
			if ($tiki_p_admin != 'y' && $userlib->object_has_one_permission($res['projectId'], 'project')) {
			        // permissions override category permissions
				if (!$userlib->object_has_permission($user, $res['projectId'], 'project', 'tiki_p_project_view')) {
					$add = FALSE;
				}
			} elseif ($tiki_p_admin != 'y' && $feature_categories == 'y') {
				// np permissions so now we check category permissions
				global $categlib;
				if (!is_object($categlib)) {
					include_once('lib/categories/categlib.php');
				}
				unset($tiki_p_view_categories); // unset this var in case it was set previously
				$perms_array = $categlib->get_object_categories_perms($user, 'project', $res['projectId']);
				if ($perms_array) {
					$is_categorized = TRUE;
					foreach ($perms_array as $perm => $value) {
						$$perm = $value;
					}
				} else {
					$is_categorized = FALSE;
				}
				if ($is_categorized && isset($tiki_p_view_categories) && $tiki_p_view_categories != 'y') {
					$add = FALSE;
				}
			}
			if ($add) {
				$ret[] = $res;
				
			}
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;

		return $retval;
	}

	// Get a Project by ID value
	function get_project_by_id ($pId) {
		if (!isset($pId)) {
			$smarty->assign('msg', 'no project id');
			$smarty->display("error.tpl");
			die;
		}

		global $feature_project_home_prefix;

		$query = "select `projectId`, `projectName`, `projectDescription`, `Created` from `tiki_projects` where `projectId` = ? and `active` = ?";
		$result = $this->query($query, array($pId, 'y'));

		$ret = $result->fetchRow();
		
		return $ret;
	}

	function get_project_members ($pId) {
		if (!isset($pId)) {
			$smarty->assign('msg', 'no project id');
			$smarty->display("error.tpl");
			die;
		}

		global $userlib, $feature_project_group_prefix_admin, $feature_project_group_prefix;
		
		$prjName = $this->get_project_name($pId);
		
		$admins = $userlib->get_group_users($feature_project_group_prefix_admin.$prjName);
		$members = $userlib->get_group_users($feature_project_group_prefix.$prjName);

		$users = array_merge($admins, $members);
		return $users;
	}

	function get_project_admins ($pId) {
		if (!isset($pId)) {
			$smarty->assign('msg', 'no project id');
			$smarty->display("error.tpl");
			die;
		}
		
		global $userlib, $feature_project_group_prefix_admin;

		$prjName = $this->get_project_name($pId);
		$admins = $userlib->get_group_users($feature_project_group_prefix_admin.$prjName);

		return $admins;

	}

	function get_project_name($pId) {
		$query = "select `projectName` from `tiki_projects` where `projectId` = ?";
		$prjName = $this->getOne($query, array($pId));

		return $prjName;
	}

	// Get the objects that belong to a project
	function get_project_objects($pId) {
		if (!isset($pId)) {
//			$pId = 0;
		}

		$query = "select `objectType`, `objectId` from `tiki_project_objects` where `projectId` = ?";
		$res = $this->query($query, array($pId));
		
		$retval = array();
		
		return $retval;
	}

	// Add object to the project
	function add_object($pId, $objType, $objId) {

		$query = "insert into `tiki_project_objects` (`projectid`, `objectType`, `objectId`) Values (?, ?, ?)";
		$res = $this->query($query, array($pId, $objType, $objId));

		return true;
	}

}

global $dbTiki;
$projectslib = new ProjectsLib($dbTiki);

?>
