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
	$perms = explode('|',$params['perms']);

	foreach ($perms as $perm) {
	    global $$perm;
	    if ($$perm == 'y')
		return $data;
	}

	return '';
}

?>
