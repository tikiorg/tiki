<?php

function wikiplugin_galaxiainstances_help() {
    return tra("TODO HELP");
}

function wikiplugin_galaxiainstances($data, $params) {

    if (isset($_REQUEST['activityId'])) {
	return '';
    }

    global $dbGalaxia, $dbTiki, $user, $smarty;
    $dbGalaxia =& $dbTiki;
    include("lib/Galaxia/GUI.php");
    include("lib/Galaxia/API.php");

    $where = '';

    if (isset($params['activityId'])) {
	$where = 'ga.activityId = '.$params['activityId'];
	$smarty->assign('activityId',$params['activityId']);
    }

    $u = isset($params['user']) ? $params['user'] : $user;
    
    $items = $GUI->gui_list_user_instances($u, 0, -1, 'procname_asc', '', $where);

    $items = $items['data'];

    if (sizeof($items) == 0) {
	return $data;
    }

    for ($i=0; $i < sizeof($items); $i++) {
	$instance->getInstance($items[$i]['instanceId']);
	$items[$i]['properties'] = $instance->properties;
    }

    $smarty->assign('instances',$items);
    $smarty->assign('galaxia_title',$params['title']);

    if (isset($params['link']) && $params['link']) {
	$smarty->assign('link',1);
    }

    return $smarty->fetch('wikiplugin_galaxiainstances.tpl');
}

?>