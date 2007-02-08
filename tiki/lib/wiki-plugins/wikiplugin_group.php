<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_group.php,v 1.5 2007-02-08 13:51:22 sylvieg Exp $
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

	if (!empty($params['groups'])) {
		$groups = explode('|',$params['groups']);
	}
	if (!empty($params['notgroups'])) {
		$notgroups = explode('|', $params['notgroups']);
	}
	if (empty($groups) && empty($notgroups)) {
		return '';
	}

	$userGroups = $userlib->get_user_groups($user);

	if ((!empty($groups) && $groups[0] == 'Anonymous' && sizeof($groups) == 1) || (!empty($notgroups) && $notgroups[0] == 'Anonymous' && sizeof($notgroups) == 1)) {
		foreach ($userGroups as $key=>$grp) {
			if ($grp == 'Anonymous') {
				$userGroups[$key] = '';
				break;
			}
		}
	}

	if (!empty($groups)) {
		$ok = false;

		foreach ($userGroups as $grp) {
		    if (in_array($grp, $groups)) {
				$ok = true;
				break;
			}
		}
		if (!$ok)
			return $dataelse;
	}
	if (!empty($notgroups)) {
		$ok = true;
		foreach ($userGroups as $grp) {
		    if (in_array($grp, $notgroups)) {
				$ok = false;
				break;
			}
		}
		if (!$ok)
			return $dataelse;
	}
		
	return $data;
}

?>
