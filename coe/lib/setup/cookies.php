<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$headerlib->add_js("tiki_cookie_jar=new Array();");

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
	if( isset($_GET['cookietab'])) {
		$smarty->assign('cookietab',$_GET['cookietab']);
		$cookietab = $_GET['cookietab'];
	} else if (count($_POST) > 0 and preg_replace(array('/\?.*$/','/^http.?:\/\//'),'',$_SERVER['HTTP_REFERER']) == preg_replace('/\?.*$/','',$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) && isset($_COOKIE['tab'])) {
		// AJAX call (probably) and same URI, reset cookietab if changed "page"
		if ($prefs['feature_ajax'] == 'y' && isset($_POST['xjxfun'])) {
			if (isset($_COOKIE['tab_last_query']) && $_COOKIE['tab_last_query'] != $_SERVER['QUERY_STRING']) {
				setcookie('tab', '1');
				$_COOKIE['tab'] = '1';
			}
			setcookie('tab_last_query', $_SERVER['QUERY_STRING']);
		}
		$smarty->assign('cookietab',$_COOKIE['tab']);
		$cookietab = $_COOKIE['tab'];
	} else if (isset($_SERVER['HTTP_REFERER']) && preg_replace(array('/\?.*$/','/^http.?:\/\//'),'',$_SERVER['HTTP_REFERER']) == preg_replace('/\?.*$/','',$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) && isset($_COOKIE['tab'])) {
		preg_match('/[\?\&]page=([^\&]*)/', $_SERVER['REQUEST_URI'], $q_match);	// TODO replace with better way to get a param?
		preg_match('/[\?\&]page=([^\&]*)/', $_SERVER['HTTP_REFERER'], $ref_match);
		if ($q_match == $ref_match) {	// for admin includes when staying on same panel
			$smarty->assign('cookietab',$_COOKIE['tab']);
			$cookietab = $_COOKIE['tab'];
		}
	} else {
		$smarty->assign('cookietab',1);
		$cookietab = '1';
	}
}
