<?php

function wikiplugin_galaxiasequence_help() {
    return tra("TODO HELP");
}

function wikiplugin_galaxiasequence($data, $params) {
    if (!isset($_REQUEST['iid'])) {
	$_REQUEST['iid'] = 0;
    }
    if (!isset($_REQUEST['auto'])) {
	$_REQUEST['auto'] = 0;
    }

    if (isset($_REQUEST['activityId'])) {
	$user_interface = galaxia_execute_activity($_REQUEST['activityId'], $_REQUEST['iid'], 0);
    } else {
	$user_interface = '<a href="tiki-index.php?page='.$_REQUEST['page'].'&activityId='.$params['startActivityId'].'">'.$params['start_message']."</a>";
    }

    if (!empty($user_interface)) {
	return '~np~'.$user_interface.'~/np~';
    } else {
	return $data;
    }
}

require_once("lib/wiki-plugins/galaxia_plugins_common.php");

?>