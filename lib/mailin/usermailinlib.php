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

/**
 *
 */
class UserMailinLib extends TikiLib
{
	/**
	 * locate_struct will find matching structure routing rules, given the email subject and body
	 *
	 * @param mixed $user Resolved Tiki user from email address
	 * @param mixed $subject Email subject
	 * @param mixed $body Email body
	 * @return mixed array - 'data', 'cant'
	 *
	 */	
	function locate_struct($user, $subject, &$body)
	{
		$result = array();
		$routes = $this->list_user_mailin_struct($user);
		foreach ($routes['data'] as $r) {
			if ($r['username'] == $user) {
				if ($r['is_active'] === 'y') {
					if ($this->matchPattern($r['subj_pattern'], $subject, $r['body_pattern'], $body)) {
						// Found a matching routing pattern for the user
						$result[] = $r;
					}
				}
			}
		}

		$retval = array();
		$retval["data"] = $result;
		$retval["cant"] = count($result);
		return $retval;
	}
	
	/**
	 * matchPattern
	 * Either subj_pattern or body_pattern or both must be specified to find a match.
	 * If both are specified, both must match. If only one is specified, it must match. The empty pattern is ignored.
	 *
	 * @param mixed $subj_pattern Pattern to match in subject
	 * @param mixed $subject The email subject text
	 * @param mixed $body_pattern Pattern to match in the body
	 * @param mixed $body The email body
	 * @return mixed boolean
	 *
	 */	
	private function matchPattern($subj_pattern, $subject, $body_pattern, &$body)
	{
		$rc1 = null;
		$rc2 = null;
		if (!empty($subj_pattern)) {
			if (stripos($subject, $subj_pattern, 0) !== false) {
				$rc1 = true;
			} else {
				$rc1 = false;
			}
		}
		if (!empty($body_pattern)) {
			if (stripos($body, $body_pattern, 0) !== false) {
				$rc2 = true;
			} else {
				$rc2 = false;
			}
		}
		if ($rc1 == null && $rc2 == null)
			return false;
		$rc1 = $rc1 == null ? $rc2 : $rc1;
		$rc2 = $rc2 == null ? $rc1 : $rc2;
		return $rc1 && $rc2;
	}
	
    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
	function list_user_mailin_struct($user, $maxRecords = -1, $offset = 0)
	{
		$bindvars = array($user);
		$query = "select u.email, mailin.*, p.pageName, s2.page_ref_id as page_struct_refid, s2.parent_id as page_struct_parentid, s.page_ref_id, s.parent_id , p2.pageName as structName
from `tiki_user_mailin_struct` mailin 
        left outer join `tiki_pages` p on p.`page_id` = mailin.`page_id` 
        left outer join `tiki_structures` s on s.`structure_id` = mailin.`structure_id` and s.`parent_id` = 0
        left outer join `tiki_pages` p2 on p2.`page_id` = s.`page_id` 
        left outer join `tiki_structures` s2 on s2.`structure_id` = mailin.`structure_id` and s2.`page_id` = mailin.`page_id`
        left outer join `users_users` u on u.login = mailin.username
where mailin.`username` = ? 
order by p2.pageName, p.pageName";

		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		
		$retval = array();
		$retval["data"] = $result->result;
		$retval["cant"] = $result->numrows;
		return $retval;
	}
	
	
	function list_all_user_mailin_struct($onlyActive = true, $maxRecords = -1, $offset = 0)
	{
		$sqlOnlyActive = '';
		if ($onlyActive) {
			$sqlOnlyActive = " and mailin.is_active = 'y' ";
		}
		$query = "select u.email, mailin.*, p.pageName, s2.page_ref_id as page_struct_refid, s2.parent_id as page_struct_parentid, s.page_ref_id, s.parent_id , p2.pageName as structName
from `tiki_user_mailin_struct` mailin 
        left outer join `tiki_pages` p on p.`page_id` = mailin.`page_id` 
        left outer join `tiki_structures` s on s.`structure_id` = mailin.`structure_id` and s.`parent_id` = 0
        left outer join `tiki_pages` p2 on p2.`page_id` = s.`page_id` 
        left outer join `tiki_structures` s2 on s2.`structure_id` = mailin.`structure_id` and s2.`page_id` = mailin.`page_id`
        left outer join `users_users` u on u.login = mailin.username
where 1 = 1
".$sqlOnlyActive."	
order by mailin.username, p2.pageName, p.pageName
";
		$bindvars = array();
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		
		$retval = array();
		$retval["data"] = $result->result;
		$retval["cant"] = $result->numrows;
		return $retval;
	}

	function add_user_mailin_struct($username, $subj_pattern, $body_pattern, $structure_id, $page_id, $is_active)
	{
		$bindvars = array($username, $subj_pattern, $body_pattern, (int)$structure_id, (int)$page_id, $is_active);
		$query = "insert into `tiki_user_mailin_struct`(`username`,`subj_pattern`,`body_pattern`,`structure_id`,`page_id`,`is_active`) values(?,?,?,?,?,?)";
		$result = $this->query($query, $bindvars);
	}

	function update_user_mailin_struct($mailin_struct_id, $username, $subj_pattern, $body_pattern, $structure_id, $page_id, $is_active)
	{
		if ($mailin_struct_id) {
			$bindvars = array($username, $subj_pattern, $body_pattern, (int)$structure_id, (int)$page_id, $is_active, (int)$mailin_struct_id);
			$query = "update `tiki_user_mailin_struct` set `username`=?, `subj_pattern`=?, `body_pattern`=?, `structure_id`=?, `page_id`=?, `is_active`=? where `mailin_struct_id`=?";
			$result = $this->query($query, $bindvars);
			return true;
		}
		return false;
	}

	function delete_user_mailin_struct($mailin_struct_id)
	{
		if ($mailin_struct_id) {
			$bindvars = array((int)$mailin_struct_id);
			$query = "delete from `tiki_user_mailin_struct` where `mailin_struct_id`=?";
			$result = $this->query($query, $bindvars, -1, -1, false);
			return true;
		}
		return false;
	}	
}
