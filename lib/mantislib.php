<?php

require_once('tiki-setup_base.php');

require_once( 'lib/mantis/core.php' );

$t_core_path = config_get( 'core_path' );

require_once( $t_core_path.'current_user_api.php' );
require_once( $t_core_path.'news_api.php' );
require_once( $t_core_path.'date_api.php' );

class MantisLib extends TikiLib {
	function MantisLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to TaskLib constructor");
		}
		$this->db = $db;
	}

	function get_assigned_open_bug_count($user, $p_project_id = ALL_PROJECTS ) {
		global $userlib;

		$p_user_id = $userlib->get_user_id($user);
		return user_get_assigned_open_bug_count($p_user_id, $p_project_id);
	}

	function get_reported_open_bug_count($user, $p_project_id = ALL_PROJECTS ) {
		global $userlib;

		$p_user_id = $userlib->get_user_id($user);
		return user_get_reported_open_bug_count($p_user_id, $p_project_id);
	}

	function project_option_list($user, $p_project_id = null, $p_include_all_projects = true) {
		global $userlib;
		$projects = array();

		$userid = $userlib->get_user_id($user);

		$t_project_ids = user_get_accessible_projects($userid);
		if ( $p_include_all_projects ) {
			$project[ALL_PROJECTS] = array();
                        $projects[ALL_PROJECTS]["id"] = ALL_PROJECTS;
                        $projects[ALL_PROJECTS]["name"] = "All Projects";
		}

		foreach ($t_project_ids as $t_id) {
			$project[$t_id] = array();
                        $projects[$t_id]["id"] = $t_id;
                        $projects[$t_id]["name"] = project_get_field( $t_id, 'name');
		}


		return $projects;
	}

	function print_reporter_option_list($user, $p_project_id = ALL_PROJECTS) {
		global $userlib;

		$userid = $userlib->get_user_id($user);

		$t_users = array();
	}

}

$mantislib = new MantisLib($dbTiki);

?>
