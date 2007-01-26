<?php
// Display wiki text if user has one of listed permissions
// Usage:
// {PERM(perms=>tiki_p_someperm|tiki_p_otherperm)}wiki text{PERM}

function wikiplugin_perm_help() {
	$help = tra("Display wiki text if user has one of listed permissions").":\n";
	$help.= "~np~{PERM(perms=>tiki_p_someperm|tiki_p_otherperm)}wiki text{PERM}~/np~";
	return $help;
}
function wikiplugin_perm($data, $params) {
	global $user, $userlib;
	if (!empty($params['perms']))
		$perms = explode('|',$params['perms']);
	if (!empty($params['notperms']))
		$notperms = explode('|', $params['notperms']);

	if (strpos($data,'{ELSE}')) {
		$dataelse = substr($data,strpos($data,'{ELSE}')+6);
		$data = substr($data,0,strpos($data,'{ELSE}'));
	} else {
		$dataelse = '';
	}

	if (!empty($perms)) {
		$ok = false;
		foreach ($perms as $perm) {
			if (!empty($params['global']) && $params['global']) {
				if ($userlib->user_has_permission($user, $perm)) {
					$ok = true;
					break;
				}
			} else {
	    		global $$perm;
	    		if ($$perm == 'y') {
					$ok = true;
					break;
				}
			}
		}
		if (!$ok)
			return $dataelse;
	}
	if (!empty($notperms)) {
		$ok = true;
		foreach ($notperms as $perm) {
			if (!empty($params['global']) && $params['global']) {
				if ($userlib->user_has_permission($user, $perm)) {
					$ok = false;
					break;
				}
			} else {
				global $$perm;
				if ($$perm == 'y') {
					$ok = false;
					break;
				}
			}
		}
		if (!$ok)
			return $dataelse;
	}

	return $data;
}

?>
