<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$headerlib->add_js("var tiki_cookie_jar=new Array();");

if ( isset($_SESSION['tiki_cookie_jar']) ) {
	$cookielist = array();

	foreach ( $_SESSION['tiki_cookie_jar'] as $nn => $vv ) {
		$cookielist[] = "$nn: '". addslashes($vv)."'";
	}

	if ( count($cookielist) ) {		
		$headerlib->add_js("tiki_cookie_jar={\n". implode(",\n\t",$cookielist)."\n};",80);	
	}
}

$smarty->assign_by_ref('cookie', $_SESSION['tiki_cookie_jar']);

// fix margins for hidden columns - css (still) doesn't work as it needs to know the "normal" margins FIXME
if (isset($_SESSION['tiki_cookie_jar']['show_col2']) and $_SESSION['tiki_cookie_jar']['show_col2'] == 'n') {
	$headerlib->add_css('#c1c2 #wrapper #col1.marginleft { margin-left: 0; }', 100);
}
if (isset($_SESSION['tiki_cookie_jar']['show_col3']) and $_SESSION['tiki_cookie_jar']['show_col3'] == 'n') {
	$headerlib->add_css('#c1c2 #wrapper #col1.marginright { margin-right: 0; }', 100);
}

function getCookie($name, $section=null, $default=null) {
	if (isset($feature_no_cookie) && $feature_no_cookie == 'y') {
		if (isset($_SESSION['tiki_cookie_jar'])) {// if cookie jar doesn't work
			if (isset($_SESSION['tiki_cookie_jar'][$name]))
				return $_SESSION['tiki_cookie_jar'][$name];
			else
				return $default;
		}
	}
	else if ($section){
		if (isset($_COOKIE[$section])) {
			if (preg_match("/@".$name."\:([^@;]*)/", $_COOKIE[$section], $matches))
				return $matches[1];
			else
				return $default;
		}
		else
			return $default;
	}
	else {
		if (isset($_COOKIE[$name]))
			return $_COOKIE[$name];
		else
			return $default;
	}
}

global $cookietab;
if ($prefs['feature_tabs'] == 'y') {
	if( isset($_REQUEST['cookietab'])) {
		$cookietab = $_REQUEST['cookietab'];

	} elseif (isset($_SERVER['HTTP_REFERER']) && preg_replace(array('/\?.*$/','/^http.?:\/\//'),'',$_SERVER['HTTP_REFERER']) == preg_replace('/\?.*$/','',$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) && isset($_COOKIE['tab'])) {

		preg_match('/[\?\&]page=([^\&]*)/', $_SERVER['REQUEST_URI'], $q_match);	// admin & wiki pages
		preg_match('/[\?\&]page=([^\&]*)/', $_SERVER['HTTP_REFERER'], $ref_match);
		
		if ((isset($_COOKIE['tab_last_query']) && $_COOKIE['tab_last_query'] == $_SERVER['SCRIPT_NAME'] . serialize($_GET)) || (count($q_match) == 0 || $q_match == $ref_match)) {	// for admin includes when staying on same panel
			$cookietab = $_COOKIE['tab'];
		}
	}
	setcookie('tab_last_query', $_SERVER['SCRIPT_NAME'] . serialize($_GET));
	
	if (empty($cookietab)) {
		$cookietab = '1';
	}
	$smarty->assign('cookietab',$cookietab);
	setcookie('tab', "$cookietab");
	$_COOKIE['tab'] = "$cookietab";
	
	// add JS to set up current tab
	$max_tikitabs = 50;
	//$headerlib->add_jq_onready("tikitabs($cookietab,$max_tikitabs);");
	
}
