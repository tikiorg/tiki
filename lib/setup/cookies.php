<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/cookies.php,v 1.1.2.2 2007-12-12 00:09:41 nkoth Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

$headerlib->add_js("tiki_cookie_jar=new Array()");

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
