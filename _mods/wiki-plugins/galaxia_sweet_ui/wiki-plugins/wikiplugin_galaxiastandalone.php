<?php
// Executes a Galaxia STANDALONE activity inside a wiki page
// Usage:
// {GALAXIASTANDALONE(activityId => ID)}{GALAXIASTANDALONE}

function wikiplugin_galaxiastandalone_help() {
	$help = tra("Executes a Galaxia standalone activity").":\n";
	$help.= "~np~{GALAXIASTANDALONE(activityId => ID)}{GALAXIASTANDALONE}~/np~";
	return $help;
}

function wikiplugin_galaxiastandalone($data, $params) {
    $user_interface = galaxia_execute_activity($params['activityId'], 0, 0);

    if (!empty($user_interface)) {
	return '~np~'.$user_interface.'~/np~';
    } else {
	return $data;
    }
}

require_once("lib/wiki-plugins/galaxia_plugins_common.php");

?>