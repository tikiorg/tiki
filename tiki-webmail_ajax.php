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

	$msg = tr('Checking').'...';
	
	// set up xajax javascript
	$headerlib->add_js( "

function doTakeWebmail(messageID) {
	
	xajax.config.requestURI = 'tiki-webmail_ajax.php';	// tell it where to send the request
	xajax.config.statusMessages = true;
	xajax.config.waitCursor = false;
	xajax_takeGroupMail('$divId', messageID);
	\$jq('#$divId .webmail_refresh_icon').hide();
	\$jq('#$divId .webmail_refresh_busy').show();
	\$jq('#$divId .webmail_refresh_message').text('$msg');
	\$jq('#$divId .webmail_refresh_message').show();


/*	var data = new Array();
	data['channels'] = new Array();
	data['channels'][0] = new Array();
	data['channels'][0]['channel_name']= 'groupmail_ticket_create';
	data['channels'][0]['trackerid']	= '12';
	data['channels'][0]['fromid']		= '42';
	data['channels'][0]['userid']		= '43';
	data['channels'][0]['subjectid']	= '44';
	data['channels'][0]['from']		= 'jonny@nospaces.net';
	data['channels'][0]['user']		= 'admin';
	data['channels'][0]['subject']	='here is the subject!';
	
	data['return_uri'] = '/aptana/tiki-trunk/tiki-view_tracker.php?trackerId=12';
	
	var str = \$jq.param(data);
	
	\$jq.post( 'tiki-channel.php', str,
	//\$jq().load( 'tiki-channel.php', data,
		function(data){
//			location.href = '/aptana/tiki-trunk/tiki-view_tracker.php?trackerId=12';
		});
*/
}

function doRefreshWebmail(start, reload) {
	if (\$jq('.box-webmail_inbox .box-data').css('display') != 'none') {
		xajax.config.requestURI = 'tiki-webmail_ajax.php';	// tell it where to send the request
		xajax.config.statusMessages = true;
		xajax.config.waitCursor = false;
		xajax_refreshWebmail('$divId', start, reload);
		\$jq('#$divId .webmail_refresh_icon').hide();
		\$jq('#$divId .webmail_refresh_busy').show();
		\$jq('#$divId .webmail_refresh_message').text('$msg');
		\$jq('#$divId .webmail_refresh_message').show();
	}
	if (typeof autoRefresh != 'undefined' && typeof doRefreshWebmail == 'function') {
		setTimeout('doRefreshWebmail()', autoRefresh);
	}
}

function initWebmail() {
	\$jq('#$divId .webmail_refresh_busy').hide();
	\$jq('#$divId .webmail_refresh_icon').show();
	\$jq('#$divId .mod_webmail_list').show('slow');
	if (jqueryTiki.tooltips) {
		//\$jq('a.tips').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400});
		\$jq('a.tips300').cluetip({splitTitle: '|', showTitle: false, width: '300px', cluezIndex: 400});
		//\$jq('a.titletips').cluetip({splitTitle: '|', cluezIndex: 400});
	}
}

\$jq('document').ready( function() {
	\$jq('#$divId .webmail_refresh_busy').hide();
	\$jq('#$divId .webmail_refresh_message').hide();
	\$jq('#$divId .mod_webmail_list').hide();
});
");
	
} else {	// end if (!isset($_REQUEST['xjxfun'])) - AJAX call

}

function refreshWebmail($destDiv = 'mod-webmail_inbox', $inStart = 0, $inReload = false) {
	global $user, $smarty, $prefs, $ajaxlib;
	
	if (isset($_SESSION['webmailinbox'][$destDiv]['module_params'])) {
		$params = $_SESSION['webmailinbox'][$destDiv]['module_params'];
	} else {
		$params = Array();	// TODO error?
	}
	if ($inReload) {
		$params['reload'] = 'y';
	}
	$params['nobox'] = 'y';
	$params['notitle'] = 'y';
	$params['np'] = '0';
	$params['module'] = 'webmail_inbox';
	if ($inStart > 0) {
		$_SESSION['webmailinbox'][$destDiv]['start'] = $inStart;
	}
	
	include('lib/wiki-plugins/wikiplugin_module.php');
	$data = wikiplugin_module('', $params);
	$objResponse = new xajaxResponse();
	$objResponse->script('setTimeout("initWebmail()",1000)');
	
	$objResponse->assign($destDiv,"innerHTML",$data);
	return $objResponse;
}

function takeGroupMail($destDiv = 'mod-webmail_inbox', $msgId) {
	global $prefs, $trklib, $user, $webmaillib, $dbTiki;
	
	if (!isset($webmaillib)) { include_once ('lib/webmail/webmaillib.php'); }
	if (!isset($trklib)) { include_once('lib/trackers/trackerlib.php'); }

	if (isset($_SESSION['webmailinbox'][$destDiv]['module_params'])) {
		$params = $_SESSION['webmailinbox'][$destDiv]['module_params'];
	} else {
		$params = Array();	// TODO error?
	}
	$accountid = isset($params["accountid"]) ? $params['accountid'] : 0;
	$ls = $webmaillib->refresh_mailbox($user, $accountid);
	
	$m = $ls[$msgId - 1];
	$from		= $m['sender']['email'];
	$subject	= $m['subject'];
	$realmsgid	= $m['realmsgid'];
	
	// maybe a pref?
	$trackerId	= $params['trackerId'];	//12;
	$fromFId = $params['fromFId'];	//42;
	$operatorFId = $params['operatorFId'];	//43;
	$subjectFId = $params['subjectFId'];	//44;
	$messageFId = $params['messageFId'];	//45;
	
	$items['data'][0]['fieldId'] = $fromFId;
	$items['data'][0]['type'] = 't';
	$items['data'][0]['value'] = $from;
	$items['data'][1]['fieldId'] = $operatorFId;
	$items['data'][1]['type'] = 'u';
	$items['data'][1]['value'] = $user;
	$items['data'][2]['fieldId'] = $subjectFId;
	$items['data'][2]['type'] = 't';
	$items['data'][2]['value'] = $subject;
	$items['data'][3]['fieldId'] = $messageFId;
	$items['data'][3]['type'] = 't';
	$items['data'][3]['value'] = $realmsgid;
	
	$trklib->replace_item($trackerId, 0, $items);
	
	$objResponse = new xajaxResponse();
	$objResponse->redirect("tiki-view_tracker.php?trackerId=$trackerId");
	return $objResponse;
}

$ajaxlib->registerFunction('refreshWebmail');
$ajaxlib->registerFunction('takeGroupMail');
$ajaxlib->processRequests();
