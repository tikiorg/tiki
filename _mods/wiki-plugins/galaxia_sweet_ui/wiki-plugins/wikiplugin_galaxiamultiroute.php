<?php
// On a Galaxia SWITCH activity, allows a user to see many instances on that activity, check its contents
// with overlib and choose next activity for each instance, with a single post.
// Usage:
// {GALAXIAMULTIROUTE(activityId => ID, labelProperty => instanceProperty, link => makeLink, route_activity_name => someLabel, route_another_activity => otherLabel, nothing => stayHereLabel, next_user_some_activity => someProperty)instanceTemplate{GALAXIAMULTIROUTE}
// ID is the activityId of a SWITCH activity.
// instanceProperty is the property of instance that will be used as title for each item in list.
// makeLink is boolean and determines if each instance will have a link for executing current activity on instance
//   as defined by Galaxia. If set to 1, you must call GALAXIASEQUENCE plugin before this, so that activity can be
//   executed in wiki page. You can let instances with no link and just complete() them from GALAXIAMULTIROUTE plugin.
// All route_* params indicate labels for next activities user can send the instance. For example, if you define:
//   route_Approved => Ok, route_Rejected => Back
//   then each instance will have two columns of radio buttons, one named "Ok" and other "Back". Clicking Ok will call
//   $instance->setNextActivity("Approved"), while Back will set next activity to Back. On submitting form, all instances
//   that have a radio checked will be completed, unless...
// "nothing" parameter defines a label for a radio button column that will not route instance anywhere, it will stay
//   in this activity with no modification. It's the same effect as not clicking any radio button of that instance row,
//   but once you have checked a radio you can't uncheck it, that's why we need this parameter.
// next_user_* params indicate instance properties that are used to check whom to send instance depending on activity.
//   For example, if you set:
//   next_user_Rejected => requester
//   Then if instance is sent to "Rejected" activity, next user will be set according to "requester" instance property.
// instanceTemplate, the data provided to wiki plugin, is an Smarty template for displaying instance data onmouseover
//   of any instance label or radio button. Inside this template, $instance is an associative array containing all
//   properties of the instance. (Note: the template is interpreted as wiki text, not html, but I just tested it with
//   html, by checking "Allow HTML" in wiki page edition).
//
// This plugin works well with GALAXIASEQUENCE, you tipically use GALAXIAMULTIROUTE to list all instances and allow
// quick routing, but you can use "link" parameter with GALAXIASEQUENCE to allow user to execute activity instead of
// just routing, depending on instance information.
//


function wikiplugin_galaxiamultiroute_help() {
    $help = tra("On a Galaxia SWITCH activity, allows a user to see many instances on that activity, check its contents
with overlib and choose next activity for each instance, with a single post").":\n";
    $help .= "~np~{GALAXIAMULTIROUTE(activityId => ID, labelProperty => instanceProperty, link => makeLink, route_activity_name => someLabel, route_another_activity => otherLabel, nothing => stayHereLabel, next_user_some_activity => someProperty)instanceTemplate{GALAXIAMULTIROUTE}~/np~\n";
    $help .= tra('ID is the activityId of a SWITCH activity.
instanceProperty is the property of instance that will be used as title for each item in list.
makeLink is boolean and determines if each instance will have a link for executing current activity on instance
  as defined by Galaxia. If set to 1, you must call GALAXIASEQUENCE plugin before this, so that activity can be
  executed in wiki page. You can let instances with no link and just complete() them from GALAXIAMULTIROUTE plugin.
All route_* params indicate labels for next activities user can send the instance. For example, if you define:
  route_Approved => Ok, route_Rejected => Back
  then each instance will have two columns of radio buttons, one named "Ok" and other "Back". Clicking Ok will call
  $instance->setNextActivity("Approved"), while Back will set next activity to Back. On submitting form, all instances
  that have a radio checked will be completed, unless...
"nothing" parameter defines a label for a radio button column that will not route instance anywhere, it will stay
  in this activity with no modification. It\'s the same effect as not clicking any radio button of that instance row,
  but once you have checked a radio you can\'t uncheck it, that\'s why we need this parameter.
next_user_* params indicate instance properties that are used to check whom to send instance depending on activity.
  For example, if you set:
  next_user_Rejected => requester
  Then if instance is sent to "Rejected" activity, next user will be set according to "requester" instance property.
instanceTemplate, the data provided to wiki plugin, is an Smarty template for displaying instance data onmouseover
  of any instance label or radio button. Inside this template, $instance is an associative array containing all
  properties of the instance. (Note: the template is interpreted as wiki text, not html, but I just tested it with
  html, by checking "Allow HTML" in wiki page edition).

This plugin works well with GALAXIASEQUENCE, you tipically use GALAXIAMULTIROUTE to list all instances and allow
quick routing, but you can use "link" parameter with GALAXIASEQUENCE to allow user to execute activity instead of
just routing, depending on instance information.');
    return $help;
}

function wikiplugin_galaxiamultiroute($data, $params) {

    // Let this plugin live with others in same page, if we're interested
    // in multirouting, activityId won't be in request, but provided as a param.
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

    $where = 'ga.activityId = '.$activityId;

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

	$instances[] = array('label' => $instance->get($params['labelProperty']),
			     'instanceId' => $item['instanceId'],
			     'properties' => $instance->properties);

	if (isset($params['link']) && $params['link']) {
	    $page = $_REQUEST['page'];
	    $url = 'tiki-index.php?page=' . urlencode($page) . '&amp;activityId=' . $activityId . '&amp;iid=' . $item['instanceId'];
	    $instances[sizeof($instances)-1]['url'] = $url;
	}
    }

    $smarty->assign('instances',$instances);

    $smarty->assign('instance_template',$data);
    return $smarty->fetch('wikiplugin_galaxiamultiroute.tpl');
}

require_once("lib/wiki-plugins/galaxia_plugins_common.php");

?>