<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// check if the current port is not 80 or 443
if (isset($_SERVER["SERVER_PORT"])) {
	if (($_SERVER['SERVER_PORT'] != 80) && ($_SERVER['SERVER_PORT'] != 443)) {
		if (( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' )) {
			$prefs['https_port'] = (int) $_SERVER['SERVER_PORT'];
		} else {
			$prefs['http_port'] = (int) $_SERVER['SERVER_PORT'];
		}
	}
}
if ( $prefs['https_port'] == 443 ) $prefs['https_port'] = '';
if ( $prefs['http_port'] == 80 ) $prefs['http_port'] = '';

// Detect if we are in HTTPS / SSL mode.
//
// Since $_SERVER['HTTPS'] will not be set on some installation, we may need to check port also.
//
// 'force_nocheck' option is used to set all absolute URI to https, but without checking if we are in https
//    This is useful in certain cases.
//    For example, this allow to have full HTTPS when using an entrance proxy that will use HTTPS connection with the client browser, but use an HTTP only connection to the server that hosts tikiwiki.
//
$https_mode = false;
if ( ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' )
	|| ( $prefs['https_port'] == '' && $_SERVER['SERVER_PORT'] == 443 )
	|| ( $prefs['https_port'] > 0 && $_SERVER['SERVER_PORT'] == $prefs['https_port'] )
	|| $prefs['https_login'] == 'force_nocheck'
) $https_mode = true;

$url_scheme = $https_mode ? 'https' : 'http';
$url_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST']  : $_SERVER['SERVER_NAME'];
list($url_host,)=preg_split('/:/',$url_host);	// Strip url port
$url_port = $https_mode ? $prefs['https_port'] : $prefs['http_port'];
$url_path = $tikiroot;
$base_host = $url_scheme.'://'.$url_host.(($url_port!='')?':'.$url_port:'');
$base_url = $url_scheme.'://'.$url_host.(($url_port!='')?':'.$url_port:'').$url_path;
$base_url_http = 'http://'.$url_host.(($prefs['http_port']!='')?':'.$prefs['http_port']:'').$url_path;
$base_url_https = 'https://'.$url_host.(($prefs['https_port']!='')?':'.$prefs['https_port']:'').$url_path;
// for <base> tag, which needs the " absolute URI that acts as the base URI for resolving relative URIs", not just the root of the site
$base_uri = !empty($_SERVER['REDIRECT_SCRIPT_URI']) ? $_SERVER['REDIRECT_SCRIPT_URI'] : isset($_SERVER['SCRIPT_URI']) ? $_SERVER['SCRIPT_URI'] : $base_url;
global $smarty;
$smarty->assign('base_uri', $base_uri);

// SSL options

if ( isset($_REQUEST['stay_in_ssl_mode_present']) || isset($_REQUEST['stay_in_ssl_mode']) ) {
	// We stay in HTTPS / SSL mode if 'stay_in_ssl_mode' has an 'y' or 'on' value
	$stay_in_ssl_mode = ( $_REQUEST['stay_in_ssl_mode'] == 'y' || $_REQUEST['stay_in_ssl_mode'] == 'on' ) ? 'y' : 'n';
} else {
	// Set default value of 'stay_in_ssl_mode' to the current mode state
	$stay_in_ssl_mode = $https_mode ? 'y' : 'n';
}

// Show the 'Stay in SSL mode' checkbox only if we are already in HTTPS
$show_stay_in_ssl_mode = $https_mode || $prefs['https_login'] == 'required' ? 'y' : 'n';
