<?php
// Executes a sequence of activities on a Galaxia instance, until process is ended or instance reaches an
// interactive activity that user cannot execute. Then display plugin data as end message.
// Usage:
// {GALAXIASEQUENCE(startActivityId => ID, startMessage => Message)}completeMessage{GALAXIASEQUENCE}
// ID is the activityId of a START activity that can start the sequence.
// Message is the text that will link to start activity, in case no activityId is passed in wiki page url.
// completeMessage is the wiki text that will be shown to user when sequence ends
// 
// Any page that uses this plugin acts as a general activity executer, user can pass any activityId and iid (instanceId)
// to url and activity will be executed on instance (if user has permission), iid being optional if activityId is 
// START activity. So, a wiki page of this plugin acts very like tiki-g-run_activity.php, but with startActivityId,
// startMessage and completeMessage you can put some context to a sequence of activities and hide that they're part
// of a workflow.
// This plugin can be used with GALAXIAMULTIROUTE and GALAXIAINSTANCES in the same page, if so only one type of
// plugin will be used. If activityId is set in url, GALAXIASEQUENCE will be used, otherwise the other plugin will be
// shown. Always put GALAXIASEQUENCE first.
// Be careful to always set next user in activities, so that no other users can interfere in the sequence.

function wikiplugin_galaxiasequence_help() {
    $help = tra("Executes a sequence of activities on a Galaxia instance, until process is ended or instance reaches an
interactive activity that user cannot execute. Then display plugin data as end message").":\n";
    $help .= "~np~{GALAXIASEQUENCE(startActivityId => ID, startMessage => Message)}completeMessage{GALAXIASEQUENCE}~/np~\n";

    $help .= tra("ID is the activityId of a START activity that can start the sequence.
Message is the text that will link to start activity, in case no activityId is passed in wiki page url.
completeMessage is the wiki text that will be shown to user when sequence ends
 
Any page that uses this plugin acts as a general activity executer, user can pass any activityId and iid (instanceId)
to url and activity will be executed on instance (if user has permission), iid being optional if activityId is 
START activity. So, a wiki page of this plugin acts very like tiki-g-run_activity.php, but with startActivityId,
startMessage and completeMessage you can put some context to a sequence of activities and hide that they're part
of a workflow.
This plugin can be used with GALAXIAMULTIROUTE and GALAXIAINSTANCES in the same page, if so only one type of
plugin will be used. If activityId is set in url, GALAXIASEQUENCE will be used, otherwise the other plugin will be
shown. Always put GALAXIASEQUENCE first.
Be careful to always set next user in activities, so that no other users can interfere in the sequence.");
    return $help;
}

function wikiplugin_galaxiasequence($data, $params) {
    if (!isset($_REQUEST['iid'])) {
	$_REQUEST['iid'] = 0;
    }

    if (isset($_REQUEST['activityId'])) {
	$user_interface = galaxia_execute_activity($_REQUEST['activityId'], $_REQUEST['iid'], 0);
    } elseif (isset($params['startActivityId'])) {
	$user_interface = '<a href="tiki-index.php?page='.$_REQUEST['page'].'&activityId='.$params['startActivityId'].'">'.$params['startMessage']."</a>";
    } else {
	return '';
    }

    if (!empty($user_interface)) {
	return '~np~'.$user_interface.'~/np~';
    } else {
	unset($_REQUEST['activityId']);
	unset($_REQUEST['iid']);
	return $data;
    }
}

require_once("lib/wiki-plugins/galaxia_plugins_common.php");

?>