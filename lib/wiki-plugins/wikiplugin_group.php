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
	$dataelse = '';
	if (strpos($data,'{ELSE}')) {
		$dataelse = substr($data,strpos($data,'{ELSE}')+6);
		$data = substr($data,0,strpos($data,'{ELSE}'));
	}

	$groups = explode('|',$params['groups']);

	$userGroups = $userlib->get_user_groups($user);

	if ($params['groups'] == 'Anonymous') {
	    $userGroups = array_shift($userGroups);
	}

	if ((sizeof($userGroups) == 1) && (sizeof($groups) == 1)) {
	    $tgroups = implode('',$groups);
	    if ($tgroups == $userGroups) {
		return $data;
	    }
	} else {
	foreach ($userGroups as $grp) {
	    if (in_array($grp, $groups))
		return $data;
	}
	}
	return $dataelse;
}

?>
