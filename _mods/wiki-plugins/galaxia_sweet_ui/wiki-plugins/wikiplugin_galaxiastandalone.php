<?php

function wikiplugin_galaxiastandalone_help() {
    return tra("TODO HELP");
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