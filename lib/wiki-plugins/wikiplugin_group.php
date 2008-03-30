<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_group.php,v 1.6.2.3 2007-11-19 22:09:20 sylvieg Exp $
// Display wiki text if user is in one of listed groups
// Usage:
// {GROUP(groups=>Admins|Developers)}wiki text{GROUP}

function wikiplugin_group_help() {
	$help = tra("Display wiki text if user is in one of listed groups").":\n";
	$help.= "~np~<br />{GROUP(groups=>Admins|Developers)}wiki text{GROUP}<br />
	{GROUP(notgroups=>Developers)}wiki text for other groups{GROUP}<br />	
	{GROUP(groups=>Registered)}wiki text{ELSE}alternate text for other groups{GROUP}~/np~";
	return $help;
}
function wikiplugin_group($data, $params) {
	global $user, $tikilib;
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

	$userGroups = $tikilib->get_user_groups($user);

	if (count($userGroups) > 1) { //take away the anonynous as everybody who is registered is anonynmous
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
