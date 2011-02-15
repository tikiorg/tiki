<?php
// Usage:
// {PERM(perms=>tiki_p_someperm|tiki_p_otherperm)}wiki text{PERM}

function wikiplugin_perm_help() {
	$help = tra("Display wiki text if user has one of listed permissions").":\n";
	$help.= "~np~{PERM(perms=>tiki_p_someperm|tiki_p_otherperm)}wiki text{PERM}~/np~";
	return $help;
}

function wikiplugin_perm_info() {
	return array(
		'name' => tra('Permissions'),
		'documentation' => tra('PluginPerm'),
		'description' => tra('Display wiki text if and only if the user has at least one of a set of permissions (or none, if using notperms).'),
		'body' => tra('Wiki text to display if conditions are met. The body may contain {ELSE}. Text after the marker will be displayed if conditions are not met.'),
		'prefs' => array('wikiplugin_perm'),
		'filter' => 'wikicontent',
		'params' => array(
			'perms' => array(
				'required' => false,
				'name' => tra('Possible Permissions'),
				'description' => tra('Pipe separated list of permissions, one of which is needed to view the default text.') . ' ' . tra('Example:') . 'tiki_p_rename|tiki_p_edit',
				'default' => '',
			),
			'notperms' => array(
				'required' => false,
				'name' => tra('Forbidden Permissions'),
				'description' => tra('Pipe separated list of permissions, any of which will cause the default text not to show.') . ' ' . tra('Example:') . 'tiki_p_rename|tiki_p_edit',
				'default' => '',
			)
		)
	);
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
