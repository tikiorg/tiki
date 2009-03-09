<?php

// $Id: $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include_once('lib/ajax/ajaxlib.php');
include_once ('lib/shoutbox/shoutboxlib.php');

function processShout($formValues, $destDiv = 'mod-shoutjax') {
	global $shoutboxlib, $user, $smarty, $prefs, $ajaxlib, $tiki_p_admin_shoutbox;
	
	if (array_key_exists('shout_msg',$formValues) && strlen($formValues['shout_msg']) > 2) {
		if (empty($user) && $prefs['feature_antibot'] == 'y' && (!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $formValues['antibotcode'])) {
			$smarty->assign('shout_error', tra('You have mistyped the anti-bot verification code; please try again.'));
			$smarty->assign_by_ref('shout_msg', $formValues['shout_msg']);
		} else {
			$shoutboxlib->replace_shoutbox(0, $user, $formValues['shout_msg']);
		}
	} else if (array_key_exists('shout_remove',$formValues) && $formValues['shout_remove'] > 0) {
		$info = $shoutboxlib->get_shoutbox($formValues['shout_remove']);
		if ($tiki_p_admin_shoutbox == 'y'  || $info['user'] == $user ) {
			$shoutboxlib->remove_shoutbox($formValues['shout_remove']);
		}
	}

	$ajaxlib->registerTemplate('mod-shoutjax.tpl');
	
	include('lib/wiki-plugins/wikiplugin_module.php');
	$data = wikiplugin_module('', Array('module'=>'shoutjax','max'=>10,'np'=>0,'nobox'=>'y','notitle'=>'y'));
	$objResponse = new xajaxResponse();
	$objResponse->assign($destDiv,"innerHTML",$data);
	return $objResponse;
}

$ajaxlib->registerFunction('processShout');
$ajaxlib->registerTemplate('mod-shoutjax.tpl');
$ajaxlib->processRequests();

?>