<?php
// This file provides a custom galaxia_execute_activity function. This function is supposed to 
// work fine for any activity execution and could substitute the on in Galaxia's config.tikiwiki.php,
// but it's not yet enough tested. It was written for Galaxia Sweet UI set of wiki plugins because
// in my server I can't make subrequests via https, so I copied code from tiki-g_run_activity.php
// to make it run in same request and by redirecting user.

// This WILL be executed before Galaxia/config.php, so our exec method will prevail
if (!function_exists('galaxia_execute_activity')) {
    function galaxia_execute_activity($activityId = 0, $iid = 0, $auto = 1)
    {
	global $__activity_completed, $smarty, $dbTiki, $dbGalaxia, $user;

	// Somehow dbGalalaxia is undefined on second run, so let's make sure
	$dbGalaxia =& $dbTiki;

	include('lib/Galaxia/API.php');

	$__activity_completed = false;

	$activity = $baseActivity->getActivity($activityId);
	$process->getProcess($activity->getProcessId());

	// Get user roles
	
	// Get activity roles
	$act_roles = $activity->getRoles();
	$user_roles = $activity->getUserRoles($user);
	
	// Only check roles if this is an interactive
	// activity
	if ($activity->isInteractive() == 'y') {
	    if (!count(array_intersect($act_roles, $user_roles))) {
		return '';
	    }
	}
	
	$source = 'lib/Galaxia/processes/' . $process->getNormalizedName(). '/compiled/' . $activity->getNormalizedName(). '.php';
	$shared = 'lib/Galaxia/processes/' . $process->getNormalizedName(). '/code/shared.php';
	
	// Existing variables here:
	// $process, $activity, $instance (if not standalone)
	
	// Include the shared code
	include ($shared);
	
	// Now do whatever you have to do in the activity
	include ($source);
	
	// Process comments
	// Is this implemented?? - lfagundes
	/*
	if (isset($_REQUEST['__removecomment'])) {
	    $__comment = $instance->get_instance_comment($_REQUEST['__removecomment']);
	    
	    if ($__comment['user'] == $user or $tiki_p_admin_workflow == 'y') {
		$area = "delinstancecomment";
		if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
		    key_check($area);
		    $instance->remove_instance_comment($_REQUEST['__removecomment']);
		} else {
		    key_get($area);
		}
	    }
	}
	
	$smarty->assign_by_ref('__comments', $__comments);
    
	if (!isset($_REQUEST['__cid']))
	    $_REQUEST['__cid'] = 0;
	
	if (isset($_REQUEST['__post'])) {
	    $instance->replace_instance_comment($_REQUEST['__cid'], $activity->getActivityId(), $activity->getName(),
						$user, $_REQUEST['__title'], $_REQUEST['__comment']);
	}
	
	$__comments = $instance->get_instance_comments();
        */

	// This goes to the end part of all activities
	// If this activity is interactive then we have to display the template
	if (!$__activity_completed && !$auto && $activity->isInteractive()) {
	    $template = $activity->getNormalizedName(). '.tpl';
	    return $smarty->fetch($process->getNormalizedName(). '/' . $template);
	} else {
	    if (!$auto && $instance->nextUser == $user) {
		header("Location: tiki-index.php?page=".urlencode($_REQUEST['page'])."&activityId=".$instance->nextActivity."&iid=".$instance->instanceId);
		exit;
	    } else { 
		return '';
	    }
	}
    }
}

?>