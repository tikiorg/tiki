<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-register.php,v 1.91.2.4 2008/03/23 14:12:05 sylvieg Exp $

/**
 * Tiki registration script
 *
 * This file takes care of user registration
 *
 * @license GNU LGPL
 * @copyright Tiki Community
 * @date created: 2002/10/8 15:54
 * @date last-modified: $Date: 2008/03/23 14:12:05 $
 */

// Initialization
require_once('tiki-setup.php');
include_once('lib/registration/registrationlib.php');
include_once('lib/notifications/notificationlib.php');

function register_error($errstr) {
	global $smarty;
	$smarty->assign('msg',$errstr);
	$smarty->display("error.tpl");
	die;
}

if ($prefs['allowRegister'] != 'y') {
	register_error(tra("Sorry, registration is not allowed"));
}

$rs=$registrationlib->get_registration_setup();

if (!$rs) {
	register_error(tra("Sorry, registration is not available"));
}

function registration_setuppage($rs) {
	global $smarty;

	$smarty->assign_by_ref('rs', $rs);
	$smarty->assign('listgroups', $rs['choicegroups']);	
	$smarty->assign_by_ref('customfields', $rs['customfields']);
}


registration_setuppage($rs);

if (isset($_REQUEST['register']) && !empty($_REQUEST['name']) && (isset($_REQUEST['pass']) || isset($_SESSION['openid_url']))) {
	check_ticket('register');

	foreach (array('name','pass','passAgain','antibotcode','passcode','chosenGroup',
				   'email','trackit','error','customfields') as $k) {
		if (isset($_REQUEST[$k])) $rq[$k]=$_REQUEST[$k];
	}

	// novalidation is set to yes if a user confirms his email is correct after tiki fails to validate it
	if (!isset($_REQUEST['novalidation'])) {
		$rq['novalidation'] = empty($_REQUEST['trackit']) ? '' : 'yes'; // yes if the user has already confirmed manually that SnowCheck is not working
	} else {
		$rq['novalidation'] = $_REQUEST['novalidation'];
	}

	if (isset($_SESSION['openid_url'])) $rq['openid_url']=$_SESSION['openid_url'];
	
	foreach ($rs['customfields'] as $custpref=>$prefvalue ) {
		if (isset($_REQUEST[$rs['customfields'][$custpref]['prefName']]))
			$rq['customfields'][$rs['customfields'][$custpref]['prefName']]=$_REQUEST[$rs['customfields'][$custpref]['prefName']];
	}

	$result=$registrationlib->register($rq, $rs);
	var_dump($result);
	if (count($result['error'])) {
		$msg=''; foreach($result['error'] as $errstr) $msg.=$errstr."<br>";
		$smarty->assign('msg', $msg);
	} else {
		$email_valid=$result['email_valid'];
		if (isset($result['userTrackerData'])) $smarty->assign('userTrackerData', $result['userTrackerData']);
		$smarty->assign('msg', $result['msg']);
	}
}


$smarty->assign('email_valid',$email_valid);

ask_ticket('register');

$_VALID = tra("Please enter a valid %s.  No spaces, more than %d characters and contain %s");

$smarty->assign('_PROMPT_UNAME', sprintf($_VALID, tra("username"), $rs['min_user_length'], "0-9,a-z,A-Z") );
$smarty->assign('_PROMPT_PASS', sprintf($_VALID, tra("password"), $rs['min_pass_length'], "0-9,a-z,A-Z") );
$smarty->assign('min_user_length', $rs['min_user_length']);
$smarty->assign('min_pass_length', $rs['min_pass_length']);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// xajax
if ($prefs['feature_ajax'] == 'y') {
    require_once("lib/ajax/ajaxlib.php");
    $ajaxlib->setRequestURI('tiki-register_ajax.php');
    $ajaxlib->registerFunction('AJAXCheckUserName');
    $ajaxlib->registerFunction('AJAXCheckMail');
    $ajaxlib->processRequests(); // I don't really want a "process" function here, but if I don't call it here, it will not registerfunctions....
}

$smarty->assign('mid','tiki-register.tpl');
$smarty->display("tiki.tpl");

?>
