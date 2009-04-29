<?php
/* $Id$
 *
 * Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 *
 * AJAXified Shoutbox module for TikiWiki 2.0 (jonnybradley for mpvolt Aug/Sept 2008)
 * 
 * Prefers Ajax enabled (Admin/Features/Experimental - feature_ajax) but will work the old way without it
 * Anonymous may need tiki_p_view_shoutbox and tiki_p_post_shoutbox setting (in Group admin)
 * Enable Admin/Wiki/Wiki Features/feature_antibot to prevent spam ("Anonymous editors must input anti-bot code")
 * 
 * Module parameters (with default values):
 * 
 * 	tooltip    = 1 (or 0)	put shout date in tooltip on user link
 *  buttontext = 'Post'
 *  waittext   = 'Please wait...'
 *  maxrows    = 5
 *  (plus all the usual ones: title, flip etc)
 * 
 * Example: tooltip=0&waittext=Hang on...&buttontext=Shout!
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

global $tikilib; require_once ('lib/tikilib.php');
global $shoutboxlib, $prefs, $tiki_p_view_shoutbox, $tiki_p_admin_shoutbox, $tiki_p_post_shoutbox, $base_url, $smarty;
include_once ('lib/shoutbox/shoutboxlib.php');

if (!function_exists('doProcessShout')) {
function doProcessShout($inFormValues) {
	global $shoutboxlib, $user, $smarty, $prefs;
	
	if (array_key_exists('shout_msg',$inFormValues) && strlen($inFormValues['shout_msg']) > 2) {
		if (empty($user) && $prefs['feature_antibot'] == 'y' && (!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $inFormValues['antibotcode'])) {
			$smarty->assign('shout_error', tra('You have mistyped the anti-bot verification code; please try again.'));
			$smarty->assign_by_ref('shout_msg', $inFormValues['shout_msg']);
		} else {
			$shoutboxlib->replace_shoutbox(0, $user, $inFormValues['shout_msg']);
		}
	}
}
}

if ($prefs['feature_ajax'] == 'y') {
	global $ajaxlib;
	require_once('lib/ajax/ajaxlib.php');
}

if ($prefs['feature_shoutbox'] == 'y' && $tiki_p_view_shoutbox == 'y') {
	if ($prefs['feature_ajax'] != 'y') {
		$setup_parsed_uri = parse_url($_SERVER['REQUEST_URI']);

		if (isset($setup_parsed_uri['query'])) {
			TikiLib::parse_str($setup_parsed_uri['query'], $sht_query);
		} else {
			$sht_query = array();
		}
	
		$shout_father = $setup_parsed_uri['path'];
	
		if (isset($sht_query) && count($sht_query) > 0) {
			$sht = array();
			foreach ($sht_query as $sht_name => $sht_val) {
				$sht[] = $sht_name . '=' . $sht_val;
			}
			$shout_father.= '?'.implode('&amp;',$sht).'&amp;';
		} else {
			$shout_father.= '?';
		}
	} else {	// $prefs['feature_ajax'] == 'y'
		$shout_father = 'tiki-shoutbox.php?';
	}

	global $smarty;
	$smarty->assign('shout_ownurl', $shout_father);
	if (isset($_REQUEST['shout_remove'])) {
		$info = $shoutboxlib->get_shoutbox($_REQUEST['shout_remove']);
		if ($tiki_p_admin_shoutbox == 'y'  || $info['user'] == $user ) {
			if ($prefs['feature_ticketlib2'] =='y') {
				$area = 'delshoutboxentry';
				if (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"])) {
					key_check($area);
					$shoutboxlib->remove_shoutbox($_REQUEST["shout_remove"]);
				} else {
					key_get($area);
				}
			} else {
				$shoutboxlib->remove_shoutbox($_REQUEST["shout_remove"]);
			}
		}
	}

	if ($tiki_p_post_shoutbox == 'y') {
		if ($prefs['feature_ajax'] == 'y') {
			if (!isset($_REQUEST['xajax'])) {	// xajaxRequestUri needs to be set to tiki-shoutbox.php in JS before calling the func
				$ajaxlib->registerFunction('processShout');
			}
		} else {
			if (isset($_REQUEST['shout_send'])) {
				doProcessShout($_REQUEST);
			}
		}
	}

	$maxrows = isset($module_params['maxrows']) ? $module_params['maxrows'] : 5;
	$shout_msgs = $shoutboxlib->list_shoutbox(0, $maxrows, 'timestamp_desc', '');	// $module_rows where?
	$smarty->assign('shout_msgs', $shout_msgs['data']);

	// Subst module parameters
	$smarty->assign('tooltip', isset($module_params['tooltip']) ? $module_params['tooltip'] : 1);
	$smarty->assign('buttontext', isset($module_params['buttontext']) ? $module_params['buttontext'] : tra('Post'));
	$smarty->assign('waittext', isset($module_params['waittext']) ? $module_params['waittext'] : tra('Please wait...'));
	
	if ($prefs['feature_ajax'] == 'y') {
		if (!isset($_REQUEST['xajax'])) {
			$ajaxlib->registerTemplate('mod-shoutbox.tpl');
		}
	}
}

?>
