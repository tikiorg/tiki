<?php

// $Id$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
global $headerlib, $ajaxlib;
require_once ('tiki-setup.php');
require_once('lib/ajax/ajaxlib.php');

if (!isset($_REQUEST['xjxfun'])) {	// "normal" (non-AJAX) page load

	$divId = 'mod-webmail_inbox'.$module_params['module_position'].$module_params['module_ord'];
	$module_params['module_id'] = $divId;
	
	$ajaxlib->registerTemplate('modules/mod-webmail_inbox.tpl');
	
	$_SESSION['webmailinbox'][$divId]['module_params'] = $module_params;
	
	// set up xajax javascript
	$headerlib->add_js( "

function doTakeWebmail(messageID) {
	
	xajax.config.requestURI = 'tiki-webmail_ajax.php';	// tell it where to send the request
	xajax.config.statusMessages = true;
	xajax.config.waitCursor = false;
	xajax_takeGroupMail('$divId', messageID);
	showWebmailMessage('".tra('Taking')."...');
	\$jq('#$divId .webmail_refresh_message').show();
}

function doPutBackWebmail(messageID) {
	
	xajax.config.requestURI = 'tiki-webmail_ajax.php';	// tell it where to send the request
	xajax.config.statusMessages = true;
	xajax.config.waitCursor = false;
	xajax_putBackGroupMail('$divId', messageID);
	showWebmailMessage('".tra('Putting back')."...');
	\$jq('#$divId .webmail_refresh_message').show();
}

function doRefreshWebmail(start, reload) {
	if (\$jq('.box-webmail_inbox .box-data').css('display') != 'none') {
		xajax.config.requestURI = 'tiki-webmail_ajax.php';	// tell it where to send the request
		xajax.config.statusMessages = true;
		xajax.config.waitCursor = false;
		xajax_refreshWebmail('$divId', start, reload);
		showWebmailMessage('".tra('Checking')."...');
	}
	if (typeof autoRefresh != 'undefined' && typeof doRefreshWebmail == 'function') {
		setTimeout('doRefreshWebmail()', autoRefresh);
	}
}

function initWebmail() {
	clearWebmailMessage();
	\$jq('#$divId .mod_webmail_list').show('slow');
	if (jqueryTiki.tooltips) {
		//\$jq('a.tips').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400});
		\$jq('a.tips300').cluetip({splitTitle: '|', showTitle: false, width: '300px', cluezIndex: 400});
		//\$jq('a.titletips').cluetip({splitTitle: '|', cluezIndex: 400});
	}
}

function clearWebmailMessage() {
	\$jq('#$divId .webmail_refresh_busy').hide();
	\$jq('#$divId .webmail_refresh_icon').show();
	\$jq('#$divId .webmail_refresh_message').hide();
	\$jq('#$divId .webmail_refresh_message').text('');
}

function showWebmailMessage(inMsg) {
	\$jq('#$divId .webmail_refresh_icon').hide();
	\$jq('#$divId .webmail_refresh_busy').show();
	\$jq('#$divId .webmail_refresh_message').text(inMsg);
	\$jq('#$divId .webmail_refresh_message').show();
}

\$jq('document').ready( function() {
	clearWebmailMessage();
	\$jq('#$divId .mod_webmail_list').hide();
});
");
	
} else {	// end if (!isset($_REQUEST['xjxfun'])) - AJAX call

}

function refreshWebmail($destDiv = 'mod-webmail_inbox', $inStart = 0, $inReload = false) {
	global $user, $smarty, $prefs, $ajaxlib, $module_params;
	
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
	$objResponse = new xajaxResponse();
	$objResponse->script('setTimeout("initWebmail()",1000)');
	
	$objResponse->assign($destDiv,"innerHTML",$data);
	return $objResponse;
}

function takeGroupMail($destDiv = 'mod-webmail_inbox', $msgId) {
	global $prefs, $trklib, $user, $webmaillib, $dbTiki, $module_params;
	
	if (!isset($webmaillib)) { include_once ('lib/webmail/webmaillib.php'); }
	if (!isset($trklib)) { include_once('lib/trackers/trackerlib.php'); }

	if (isset($_SESSION['webmailinbox'][$destDiv]['module_params'])) {
		$module_params = $_SESSION['webmailinbox'][$destDiv]['module_params'];
	} else {
		$module_params = Array();	// TODO error?
	}
	$accountid = isset($module_params["accountid"]) ? $module_params['accountid'] : 0;
	$ls = $webmaillib->refresh_mailbox($user, $accountid, false);
	$cont = $webmaillib->get_mail_content($user, $accountid, $msgId);
	
	$m = $ls[$msgId - 1];
	$from		= $m['sender']['email'];
	$subject	= $m['subject'];
	$realmsgid	= $m['realmsgid'];
	
	$items['data'][0]['fieldId'] = $module_params['fromFId'];
	$items['data'][0]['type'] = 't';
	$items['data'][0]['value'] = $from;
	$items['data'][1]['fieldId'] = $module_params['operatorFId'];
	$items['data'][1]['type'] = 'u';
	$items['data'][1]['value'] = $user;
	$items['data'][2]['fieldId'] = $module_params['subjectFId'];
	$items['data'][2]['type'] = 't';
	$items['data'][2]['value'] = $subject;
	$items['data'][3]['fieldId'] = $module_params['messageFId'];
	$items['data'][3]['type'] = 't';
	$items['data'][3]['value'] = $realmsgid;
	$items['data'][4]['fieldId'] = $module_params['contentFId'];
	$items['data'][4]['type'] = 'a';
	$items['data'][4]['value'] = '~pp~'.htmlentities($cont).'~/pp~';	// sigh - no option for non-wiki text :(
	
	$trklib->replace_item($module_params['trackerId'], 0, $items);
	
	$objResponse = new xajaxResponse();
	$objResponse->redirect('tiki-view_tracker.php?trackerId='.$module_params['trackerId']);
	return $objResponse;
}

function putBackGroupMail($destDiv = 'mod-webmail_inbox', $msgId) {
	global $prefs, $trklib, $user, $webmaillib, $dbTiki, $module_params;
	
	if (!isset($webmaillib)) { include_once ('lib/webmail/webmaillib.php'); }
	if (!isset($trklib)) { include_once('lib/trackers/trackerlib.php'); }

	if (isset($_SESSION['webmailinbox'][$destDiv]['module_params'])) {
		$module_params = $_SESSION['webmailinbox'][$destDiv]['module_params'];
	} else {
		$module_params = Array();	// TODO error?
	}
	$accountid = isset($module_params["accountid"]) ? $module_params['accountid'] : 0;
	$ls = $webmaillib->refresh_mailbox($user, $accountid, false);
	
	$m = $ls[$msgId - 1];
	
	$itemid = $trklib->get_item_id( $module_params['trackerId'], $module_params['messageFId'], $m['realmsgid']);
	if ($itemid > 0 && $user == $trklib->get_item_value($module_params['trackerId'], $itemid, $module_params['operatorFId'])) {	// simple security check
		$trklib->remove_tracker_item($itemid);
	}
	
	$objResponse = new xajaxResponse();
	$objResponse->script('doRefreshWebmail();');
	return $objResponse;
}

$ajaxlib->registerFunction('refreshWebmail');
$ajaxlib->registerFunction('takeGroupMail');
$ajaxlib->registerFunction('putBackGroupMail');
$ajaxlib->processRequests();
