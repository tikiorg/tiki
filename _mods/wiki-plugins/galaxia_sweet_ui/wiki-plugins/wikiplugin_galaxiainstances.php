<?php
// Shows a list of Galaxia user instances.
// Usage:
// {GALAXIAINSTANCES(activityId => ID, labelProperty => instanceProperty, title => listTitle, link => makeLink, user => userLogin)}blankListMessage{GALAXIAINSTANCES}
// ID is an activityId, if set will restrict instance list to this activity only.
// instanceProperty is the property of instance that will be used as title for each item in list.
// listTitle is the text that will appear above the list.
// makeLink is boolean and determines if each instance will have a link for executing current activity on instance.
//   If set to 1, you must call GALAXIASEQUENCE plugin before this, so that activity can be executed in wiki page.
// userLogin allows you to list instances that are available to other user. If you set this, link will automatically
//   be false. Defaults to logged user.
// blankListMessage is a message that will be shown if no instances are shown.
//
// This plugin works well with GALAXIASEQUENCE, you tipically use GALAXIAINSTANCES to list all user instances then
// execute activities on these, all inside the same wiki page.
//
// NOTE: "user" param may be bad, because allows any user with tiki_p_edit to list other user's instances. I need
// it, but if this compromises you environment, check php code below to disable this.

function wikiplugin_galaxiainstances_help() {
    $help = tra("Shows a list of Galaxia user instances").":\n";
    $help .= "~np~{GALAXIAINSTANCES(activityId => ID, labelProperty => instanceProperty, title => listTitle, link => makeLink, user => userLogin)}blankListMessage{GALAXIAINSTANCES}~/np~\n";
    $help .= tra("ID is an activityId, if set will restrict instance list to this activity only.
instanceProperty is the property of instance that will be used as title for each item in list.
listTitle is the text that will appear above the list.
makeLink is boolean and determines if each instance will have a link for executing current activity on instance.
  If set to 1, you must call GALAXIASEQUENCE plugin before this, so that activity can be executed in wiki page.
userLogin allows you to list instances that are available to other user. If you set this, link will automatically
  be false. Defaults to logged user.
blankListMessage is a message that will be shown if no instances are shown.

This plugin works well with GALAXIASEQUENCE, you tipically use GALAXIAINSTANCES to list all user instances than
execute activities on these, all inside the same wiki page.");
    return $help;
}

function wikiplugin_galaxiainstances($data, $params) {

    // Let this plugin live with GALAXIASEQUENCE in same page
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

    $u = $user;

    if (isset($params['user']) && $params['user']) {
	// COMMENT 2 LINES BELOW TO DISABLE "USER" param
	$u = $params['user'];
	$params['link'] = false;
    }

    $items = $GUI->gui_list_user_instances($user, 0, -1, 'procname_asc', '', $where);

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
    } else {
	$smarty->assign('link',0);
    }

    $smarty->assign('labelProperty',$params['labelProperty']);

    return $smarty->fetch('wikiplugin_galaxiainstances.tpl');
}

?>