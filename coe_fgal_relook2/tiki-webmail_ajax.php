<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

global $headerlib, $ajaxlib, $access;
require_once ('tiki-setup.php');
require_once('lib/ajax/ajaxlib.php');

$access->check_feature( array('feature_webmail', 'ajax_xajax' ) );
$access->check_permission_either( array('tiki_p_use_webmail', 'tiki_p_use_group_webmail') );

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
	\$('#$divId .webmail_refresh_message').show();
}

function doPutBackWebmail(messageID) {
	
	xajax.config.requestURI = 'tiki-webmail_ajax.php';	// tell it where to send the request
	xajax.config.statusMessages = true;
	xajax.config.waitCursor = false;
	xajax_putBackGroupMail('$divId', messageID);
	showWebmailMessage('".tra('Putting back')."...');
	\$('#$divId .webmail_refresh_message').show();
}

var refreshWebmailRequest;

function doRefreshWebmail(start, reload) {
	if (\$('.box-webmail_inbox .box-data').css('display') != 'none') {
		if (\$('#$divId .webmail_refresh_busy').css('display') == 'none') {
			xajax.config.requestURI = 'tiki-webmail_ajax.php';	// tell it where to send the request
			xajax.config.statusMessages = true;
			xajax.config.waitCursor = false;
			
			// set up a local callback
			refreshWebmailCallback = xajax.callback.create();
			refreshWebmailCallback.onRequest = function (oRequest) { refreshWebmailRequest = oRequest; }
			refreshWebmailCallback.onComplete = function (oRequest) { refreshWebmailRequest = false; }
			// and a global one so oither AJAX request cancel mail checking (also doesn't seem to really speed things up so far...)
			xajax.callback.global.onRequest = function() { cancelRefreshWebmail(); };
			
			xajax_refreshWebmail('$divId', start, reload);
			showWebmailMessage('".tra('Checking')."...');
		} else {
			cancelRefreshWebmail();
		}
	}
	if (typeof autoRefresh != 'undefined' && typeof doRefreshWebmail == 'function') {
		setTimeout('doRefreshWebmail()', autoRefresh);
	}
}

function cancelRefreshWebmail() {
	if (refreshWebmailRequest) {
		xajax.abortRequest(refreshWebmailRequest);
		showWebmailMessage('".tra('Aborted')."...');
		setTimeout('clearWebmailMessage();', 1000);
	}
}

function initWebmail() {
	clearWebmailMessage();
	\$('#$divId .mod_webmail_list').show('slow');
	if (jqueryTiki.tooltips) {
		//\$('a.tips').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400});
		\$('a.tips300').cluetip({splitTitle: '|', showTitle: false, width: '300px', cluezIndex: 400});
		//\$('a.titletips').cluetip({splitTitle: '|', cluezIndex: 400});
	}
}

function clearWebmailMessage() {
	\$('#$divId .webmail_refresh_busy').hide();
	\$('#$divId .webmail_refresh_icon').show();
	\$('#$divId .webmail_refresh_message').hide();
	\$('#$divId .webmail_refresh_message').text('');
}

function showWebmailMessage(inMsg) {
	\$('#$divId .webmail_refresh_icon').hide();
	\$('#$divId .webmail_refresh_busy').show();
	\$('#$divId .webmail_refresh_message').text(inMsg);
	\$('#$divId .webmail_refresh_message').show();
}

\$('document').ready( function() {
	clearWebmailMessage();
	\$('#$divId .mod_webmail_list').hide();
});

\$(window).unload( function() {
	// doesn't seem to help - gets processed after doRefreshWebmail anyway
	cancelRefreshWebmail();
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

function takeGroupMail($destDiv = 'mod-webmail_inbox', $msgId = 1) {
	global $prefs, $trklib, $user, $webmaillib, $contactlib, $dbTiki, $tikilib, $categlib, $module_params;
	
	include_once ('lib/webmail/webmaillib.php');
	include_once ('lib/webmail/contactlib.php');
	include_once('lib/trackers/trackerlib.php');
	include_once('lib/categories/categlib.php');
	
	if (isset($_SESSION['webmailinbox'][$destDiv]['module_params'])) {
		$module_params = $_SESSION['webmailinbox'][$destDiv]['module_params'];
	} else {
		$module_params = Array();	// TODO error?
	}
	$accountid = isset($module_params["accountid"]) ? $module_params['accountid'] : 0;
	$ls = $webmaillib->refresh_mailbox($user, $accountid, false);
	$cont = $webmaillib->get_mail_content($user, $accountid, $msgId);
	$acc = $webmaillib->get_webmail_account($user, $accountid);
	
	// make tracker item
	$m = $ls[$msgId - 1];
	$from		= $m['from'];
	$subject	= $m['subject'];
	$realmsgid	= $m['realmsgid'];
	$maildate	= $m['date'];
	$maildate	= strtotime($maildate);
	
	$objResponse = new xajaxResponse();
	
	// check if already taken
	$itemid = $trklib->get_item_id( $module_params['trackerId'], $module_params['messageFId'], $realmsgid);
	if ($itemid > 0) {
		$objResponse->script('doRefreshWebmail();alert("Sorry, that mail has been taken by another operator. Refreshing list...");');
		
	} else {
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
		$items['data'][4]['value'] = htmlentities($cont['body']);	// sigh - no option for non-wiki text :( - not ~pp~ wrapped anymore, made it worse :(
		$items['data'][5]['fieldId'] = $module_params['accountFId'];
		$items['data'][5]['type'] = 't';
		$items['data'][5]['value'] = $acc['account'];
		$items['data'][6]['fieldId'] = $module_params['datetimeFId'];
		$items['data'][6]['type'] = 'f';	// f?
		$items['data'][6]['value'] = $maildate;
		$trklib->replace_item($module_params['trackerId'], 0, $items);
		
	}
	
	// make name for wiki page
	$pageName = str_replace('@', '_AT_', $m['sender']['email']);
	$contId = $contactlib->get_contactId_email($m['sender']['email'], $user);
	
	// add or update (?) contact
	$ext = $contactlib->get_ext_by_name($user, tra('Wiki Page'), $contId);
	if (!$ext) {
		$contactlib->add_ext($user, tra('Wiki Page'), true);	// a public field
		$ext = $contactlib->get_ext_by_name($user, tra('Wiki Page'), $contId);
	}
	
	$arr = explode(" ", trim(html_entity_decode($m['sender']['name']), '"\' '), 2);
	if (count($arr) < 2) {
		$arr[] = '';
	}
	$contactlib->replace_contact($contId, $arr[0], $arr[1], $m['sender']['email'], '', $user, array($module_params['group']), array($ext['fieldId'] => $pageName), true);
	if (!$contId) {
		$contId = $contactlib->get_contactId_email($m['sender']['email'], $user);
	}
	
	// make or update wiki page
	global $wikilib; include_once('lib/wiki/wikilib.php');
	
	if (!$wikilib->page_exists($pageName)) {
		$comment = 'Generated by GroupMail on '.date(DATE_RFC822);
		$description = "Page $comment for ".$m['sender']['email'];
		$data = '!GroupMail case with '.$m['sender']['email']."\n";
		$data .= "''$comment''\n\n";
		$data .= "!!Info\n";
		$data .= "Contact info: [tiki-contacts.php?contactId=$contId|".$m['sender']['name']."]\n\n";
		$data .= "!!Logs\n";
		$data .= '{trackerlist trackerId="'.$module_params['trackerId'].'" '.
					'fields="'.$module_params['fromFId'].':'.$module_params['operatorFId'].':'.$module_params['subjectFId'].':'.$module_params['datetimeFId'].'" '.
					'popup="'.$module_params['fromFId'].':'.$module_params['contentFId'].'" stickypopup="n" showlinks="y" shownbitems="n" showinitials="n"'.
					'showstatus="n" showcreated="n" showlastmodif="n" filterfield="'.$module_params['fromFId'].'" filtervalue="'.$m['sender']['email'].'"}';
		$data .= "\n\n";
				
		$tikilib->create_page($pageName, 0, $data, $tikilib->now, $comment, $user, $tikilib->get_ip_address(),$description);
		$categlib->update_object_categories(array($categlib->get_category_id('Help Team Pages')), $pageName, 'wiki page');		// TODO remove hard-coded cat name
	}
	
	$objResponse->redirect($wikilib->sefurl($pageName));
	
	return $objResponse;
}

function putBackGroupMail($destDiv = 'mod-webmail_inbox', $msgId = 1) {
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
	$objResponse->script('clearWebmailMessage(); doRefreshWebmail();');
	return $objResponse;
}

$ajaxlib->registerFunction(array('refreshWebmail', array('callback' => 'refreshWebmailCallback')));
$ajaxlib->registerFunction('takeGroupMail');
$ajaxlib->registerFunction('putBackGroupMail');
$ajaxlib->processRequests();
