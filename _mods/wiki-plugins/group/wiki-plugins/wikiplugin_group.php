<?php
// Display wiki text if user is in one of listed groups
// Usage:
// {GROUP(groups=>Admins|Developers)}wiki text{GROUP}

function wikiplugin_group_help() {
	$help = tra("Display wiki text if user is in one of listed groups").":\n";
	$help.= "~np~{GROUP(groups=>Admins|Developers)}wiki text{GROUP}~/np~";
	return $help;
}
function wikiplugin_group($data, $params) {
        global $user, $userlib;

	$groups = explode('|',$params['groups']);

	$userGroups = $userlib->get_user_groups($user);

	foreach ($userGroups as $grp) {
	    if (in_array($grp, $groups))
		return $data;
	}

	return '';
}

?>
