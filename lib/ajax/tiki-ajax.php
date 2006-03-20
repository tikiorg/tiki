<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/ajax/tiki-ajax.php,v 1.2 2006-03-20 06:15:11 lfagundes Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

global $feature_ajax;
if ($feature_ajax == 'y') {
    require_once("lib/ajax/xajax.inc.php");

    function loadComponent($template, $htmlElementId) {
	global $smarty;
	$content = $smarty->fetch($template);
	$objResponse = new xajaxResponse();
	$objResponse->addAssign($htmlElementId, "innerHTML", $content);
	return $objResponse;
    }
} else {
    class xajax {
	function xajax() {}
	function registerFunction() {}
	function processRequests() {}
	function getJavascript() { return ''; }
    }
}


global $xajax;
$xajax = new xajax();

$xajax->registerFunction("loadComponent");
//$xajax->debugOn();
$smarty->assign("xajax_js",$xajax->getJavascript('','lib/ajax/xajax_js/xajax.js'));

/* vim: set expandtab: */

?>