<?php

function wikiplugin_galaxiamultiroute_help() {
    return tra("TODO HELP");
}

function wikiplugin_galaxiamultiroute($data, $params) {

    // Let this plugin live with others in same page, if we're interested
    // in multirouting, activityId won't be in request. It is provided as a param.
    if (isset($_REQUEST['activityId'])) {
	return '';
    }

    if (!isset($params['activityId'])) {
	return 'ERROR: you must provide "activityId" param';
    }

    $activityId = (int)$params['activityId'];

    global $dbGalaxia, $dbTiki, $user, $smarty;
    $dbGalaxia =& $dbTiki;
    include("lib/Galaxia/GUI.php");
    include("lib/Galaxia/API.php");

    $activity = $baseActivity->getActivity($activityId);
    
    $type = $activity->getType();

    if (!in_array($type, array('switch','activity'))) {
	return tra('Activity must be switch or activity');
    }

    $smarty->assign('type',$type);

    if (isset($_POST['route'])) {
	foreach ($_POST as $key => $value) {
	    if (preg_match('/^route_(\d+)$/',$key, $m)) {
		$instanceId = $m[1];
		$nextActivity = $value;
		if (!empty($nextActivity)) {
		    $instance->getInstance($instanceId);
		    if ($type == 'switch') {
			$instance->setNextActivity($nextActivity);
		    }
		    if (isset($params['next_user_'.$nextActivity])) {
			$instance->setNextUser($instance->get($params['next_user_'.$nextActivity]));
		    } elseif (isset($params['next_user'])) {
			$instance->setNextUser($instance->get($params['next_user']));
		    }
		    $_REQUEST['iid'] = $instanceId;
		    $instance->complete($activityId);
		}
	    }
	}
    }

    $where = 'ga.activityId = '.$params['activityId'];

    $items = $GUI->gui_list_user_instances($user, 0, -1, 'procname_asc', '', $where);

    $items = $items['data'];

    $candidates = array();
    foreach ($params as $key => $value) {
	if (preg_match("/^route_(.+)$/",$key,$m)) {
	    $candidates[] = array('value' => $m[1],
				  'label' => $value);
	}
	if ($key == 'nothing') {
	    $candidates[] = array('value' => '',
				  'label' => $value);
	}
    }
    $smarty->assign('candidates',$candidates);

    $instances = array();
    foreach ($items as $item) {
	$instance->getInstance($item['instanceId']);

	$instances[] = array('label' => $instance->get($params['title']),
			     'instanceId' => $item['instanceId'],
			     'properties' => $instance->properties);
    }

    $smarty->assign('instances',$instances);

    $smarty->assign('instance_template',$data);
    return $smarty->fetch('wikiplugin_galaxiamultiroute.tpl');
}

require_once("lib/wiki-plugins/galaxia_plugins_common.php");

?>