<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
/**
 * Tests whether a user is in a specific group, usage:
 *
 * 		{if 'Admins'|in_group}...
 * or
 * 		{if 'Group Name'|in_group:'testuser'}...
 *
 * @param string $group		group name to test (string being "modified")
 * @param string $auser		user name to check if not current logged in user
 * @return bool
 * @throws Exception
 */

function smarty_modifier_in_group($group, $auser = '') {
	global $user;

	if (! $auser) {
		$auser = $user;
	}
	return TikiLib::lib('user')->user_is_in_group($auser, $group);
}
