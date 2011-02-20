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

function setCookieSection($name, $value, $section = '', $expire = '', $path = '', $domain = '', $secure = '') {
	if ($section) {
		$valSection = getCookie($section);
		$name2 = "@" . $name . ":";
		if ($valSection) {
			if (preg_match( '/' . preg_quote($name2) . '/', $valSection)) {
				$valSection  = preg_replace( '/' . preg_quote($name2) . '[^@;]*/', $name2 + $value, $valSection );
			} else {
				$valSection = $valSection + $name2 + $value;
			}
			setCookieSection($section, $valSection, '', $expire, $path, $domain, $secure);
		}
		else {
			$valSection = $name2 . $value;
			setCookieSection($section, $valSection, '', $expire, $path, $domain, $secure);
		}

	} else {
		setcookie($name, $value, $expire, $path, $domain, $secure);
	}
}

