<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

global $headerlib, $ajaxlib, $access;
require_once ('tiki-setup.php');

$access->check_feature( array('feature_webmail', 'feature_ajax', 'ajax_todo_placeholder' ) );	// AJAX_TODO
$access->check_permission_either( array('tiki_p_use_webmail', 'tiki_p_use_group_webmail') );

$divId = 'mod-webmail_inbox'.$module_params['module_position'].$module_params['module_ord'];
$module_params['module_id'] = $divId;

$ajaxlib->registerTemplate('modules/mod-webmail_inbox.tpl');

$_SESSION['webmailinbox'][$divId]['module_params'] = $module_params;

function refreshWebmail($destDiv = 'mod-webmail_inbox', $inStart = 0, $inReload = false)
{
	global $user, $smarty, $prefs, $module_params;
	
	if (isset($_SESSION['webmailinbox'][$destDiv]['module_params'])) {
		$module_params = $_SESSION['webmailinbox'][$destDiv]['module_params'];
	} else {
		$module_params = Array();	// TODO error?
	}

	if ($inReload) {
		$module_params['reload'] = 'y';
	}

	$module_params['nobox'] = 'y';
	$module_params['notitle'] = 'y';
	$module_params['np'] = '0';
	$module_params['module'] = 'webmail_inbox';

	if ($inStart > 0) {
		$_SESSION['webmailinbox'][$destDiv]['start'] = $inStart;
	}
	
	include('lib/wiki-plugins/wikiplugin_module.php');
	$data = wikiplugin_module('', $module_params);
}
