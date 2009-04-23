<?php

// $Id: $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
global $headerlib, $ajaxlib;
require_once ('tiki-setup.php');
require_once('lib/ajax/ajaxlib.php');

if (!isset($_REQUEST['xjxfun'])) {	// don't do all this when actually on an ajax call
	// set up xajax javascript
	$module_position = $module_params['module_position'];
	$module_ord = $module_params['module_ord'];
	
	$msg = tr('Contacting mail server');
	$headerlib->add_js( <<<JS
function doRefreshWebmail(reload) {
	xajax.config.requestURI = "tiki-webmail_ajax.php";	// tell it where to send the request
	xajax.config.statusMessages = true;
	xajax.config.waitCursor = false;
	xajax_refreshWebmail("mod-webmail_inbox$module_position$module_ord", reload);
	\$jq('#webmail_refresh_icon').hide();
	\$jq('#webmail_refresh_busy').show();
	\$jq('#webmail_refresh_message').text('$msg');
	\$jq('#webmail_refresh_message').show();
	if (typeof autoRefresh != 'undefined') {
		setTimeout("doRefreshWebmail()", autoRefresh);
	}
}
function doReloadWebmail() {
	doRefreshWebmail(true);
}
function initWebmail() {
	\$jq('#webmail_refresh_busy').hide();
	\$jq('#webmail_refresh_icon').show();
	if (jqueryTiki.tooltips) {
		\$jq('a.tips').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400});
		\$jq('a.tips300').cluetip({splitTitle: '|', showTitle: false, width: '300px', cluezIndex: 400});
		\$jq('a.titletips').cluetip({splitTitle: '|', cluezIndex: 400});
	}
}
\$jq('document').ready( function() {
	\$jq('#webmail_refresh_busy').hide();
	\$jq('#webmail_refresh_message').hide();
});
JS
	);
}

function refreshWebmail($destDiv = 'mod-webmail_inbox', $inReload = false) {
	global $user, $smarty, $prefs, $ajaxlib;

	include('lib/wiki-plugins/wikiplugin_module.php');
	$data = wikiplugin_module('', Array('module'=>'webmail_inbox','max' => 10,'np' => 0,'nobox' => 'y','notitle' => 'y', 'reload' => $inReload ? 'y' : 'n'));
	$objResponse = new xajaxResponse();
	$objResponse->script('setTimeout("initWebmail()",1000)');
	//if ($repeatSeconds > 0) {
	//	$objResponse->script("setTimeout('doRefreshWebmail()',$repeatSeconds)");
	//}
	$objResponse->assign($destDiv,"innerHTML",$data);
	return $objResponse;
}

if (!isset($_REQUEST['xjxfun'])) {
	$ajaxlib->registerTemplate('modules/mod-webmail_inbox.tpl');
}
$ajaxlib->registerFunction('refreshWebmail');
$ajaxlib->processRequests();

//if (jqueryTiki.tooltips) {	// apply "cluetips" to all .tips class anchors
//\jq('#$destDiv div').load( function = {
//alert("qwe tips loaded");
//		\$jq('a.tips').cluetip({splitTitle: '|', showTitle: false, width: '150px', cluezIndex: 400});
//		\$jq('a.titletips').cluetip({splitTitle: '|', cluezIndex: 400});
//	});
//}

?>