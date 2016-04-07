<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));


global $https_mode, $url_scheme, $url_host, $url_port, $url_path, $base_host, $base_url, $base_url_http, $base_url_https, $tikiroot;

// $_SERVER['HTTP_HOST'] 172.20.20.20:8080  // reverse proxy
// $_SERVER['SERVER_PORT'] 8080 // reverse proxy
// $_SERVER['SERVER_NAME'] 172.20.20.20 // reverse proxy
// $_SERVER['SERVER_ADDR'] 172.20.20.150 // origin server
// $_SERVER['HTTP_X_FORWARDED_PROTO'] https // reverse proxy
// $_SERVER['HTTP_X_FORWARDED_FOR'] 172.20.20.150 // origin server
// $_SERVER['REQUEST_SCHEME'] http // origin server - not reliable

/**
 * Reverse Proxy Support - How it works
 * If tiki is setup behind a reverse proxy (or ssl offloader) the the follwog cases are possible:
 * user -> https -> reverse proxy -> https -> origin server
 * user -> https -> reverse proxy -> http -> origin server
 * https / http CAN be standard ports like 80 / 443 or non-standard.
 * 
 * The rewrite for $base_url and $base_url_https will be used to create the login action url. Anything else in tiki uses relative urls.
 * One exception might be the footer "The original document can be found ...."
 * 
 * The current prefs dictate that:
 * - the use of a reverse proxy must be explicit configured.
 * - that if non standard ports are used, they must be configured in $prefs['http_port'] and $prefs['https_port']
 * Technically, one could check for a reverse proxy header and then decide wether to enable / disable reverse proxy support.
 * 
 * This implementation follows the definition of the exiting prefs as they are documented.
 * That is:
 * - reverse proxy must be configured (enabled)$prefs['feature_port_rewriting']
 * - port belonging to the requested protocol is autodetected and overwrites pref set.
 * - non standard http port must be configured $prefs['http_port']  to allow sitch in case of improper protocol request
 * - non standard https port must be configured $prefs['https_port'] to allow sitch in case of improper protocol request
 */

// set defaults
$prefs['http_port'] = isset($prefs['http_port']) ? (int) $prefs['http_port'] : 80;
$prefs['https_port'] = isset($prefs['https_port']) ? (int) $prefs['https_port'] : 443;
$https_mode = false;
$reverse_proxy = false;
// Check if behind a reverse-proxy / ssl-offloader / frontend-proxy / load-balancer which rewrites ports / protocol
if (isset($prefs['feature_port_rewriting']) && $prefs['feature_port_rewriting'] == 'y' && isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
	$reverse_proxy = true;
	// We assume that this combination is valid (protocol / port) because our reverse proxy and origin server have responded.
	// so allow requested port for that protocol type - gives more flexibility and will work out of the box.
	if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == "http") {
		$prefs['http_port'] = $_SERVER['SERVER_PORT'];
		$https_mode = false;
	} else if($_SERVER['HTTP_X_FORWARDED_PROTO'] == "https") {
		// for whatever reason it can happen, that $_SERVER['SERVER_PORT'] is still set to a default 80, although the request is made to the default 443.
		// the only way to detect this seem to be to look for the HTTP_X_FORWARDED_PROTO == 'https'.
		// this impacts in particular the creation of the login url.
		if ($_SERVER['SERVER_PORT'] == 80) {
			// do nothing - keep the default as set in prefs.
		} else {
			$prefs['https_port'] = $_SERVER['SERVER_PORT'];
		}
		$https_mode = true;
	}
}
// $https_mode and $pref of the corresponding port being used are set, if reverse proxy is involved.


// Now detect if we are in HTTPS / SSL mode, if there is no reverse proxy
// Since $_SERVER['HTTPS'] will not be set on some installation, we may need to check port also.
// 'force_nocheck' option is used to set all absolute URI to https, but without checking if we are in https
//    This is useful in certain cases.
//    For example, this allow to have full HTTPS when using an entrance proxy that will use HTTPS connection with the client browser, but use an HTTP only connection to the server that hosts tikiwiki.
if (!$reverse_proxy) {
	if (
			// we have an https request
			( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' )
			// https_port is NOT set and request 443
			|| ( $prefs['https_port'] == '' && isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443 )
			// https_port is set and request matches pref
			|| ( $prefs['https_port'] > 0 && isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == $prefs['https_port'] )
			// https_login == 'force_nocheck'
			|| $prefs['https_login'] == 'force_nocheck'
	) {
		$https_mode = true;
	}
	

	// when doing a database update via console.php $_SERVER['SERVER_PORT'] is not set. So define a default to avoid notice.
	$_SERVER['SERVER_PORT'] = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
	// adjust in case the current port is not 80 or 443
	if ($https_mode) {
		$prefs['https_port'] = (int) $_SERVER['SERVER_PORT'];
	} else {
		$prefs['http_port'] = (int) $_SERVER['SERVER_PORT'];
	}
}

// reset prefs if they are on the defaults
if ( $prefs['https_port'] == 443 ) {
	$prefs['https_port'] = '';
}
if ( $prefs['http_port'] == 80 ) {
	$prefs['http_port'] = '';
}



// create $base_url_http and $base_url_https etc.
// Note: $url_scheme becomes a globally used var - even headerlib provides a method for it - so do not rename!
$url_scheme = $https_mode ? 'https' : 'http';

// depends on reverse proxy - could also use $reverse_proxy
$url_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST']  : $_SERVER['SERVER_NAME'];
list($url_host,)=preg_split('/:/', $url_host);	// Strip url port
$url_port = $https_mode ? $prefs['https_port'] : $prefs['http_port'];
$url_path = $tikiroot;
$base_host = $url_scheme.'://'.$url_host.(($url_port!='')?':'.$url_port:'');
$base_url = $url_scheme.'://'.$url_host.(($url_port!='')?':'.$url_port:'').$url_path;
$base_url_http = 'http://'.$url_host.(($prefs['http_port']!='')?':'.$prefs['http_port']:'').$url_path;
$base_url_https = 'https://'.$url_host.(($prefs['https_port']!='')?':'.$prefs['https_port']:'').$url_path;
// for <base> tag, which needs the " absolute URI that acts as the base URI for resolving relative URIs", not just the root of the site
if (!empty($_SERVER['REQUEST_URI'])) {
	$base_uri = $base_host . $_SERVER['REQUEST_URI'];
} else if (!empty($_SERVER['SCRIPT_NAME'])) {
	$base_uri = $base_host . $_SERVER['SCRIPT_NAME'];
	if (!empty($_SERVER['QUERY_STRING'])) {
		$base_uri .= '?' . str_replace('?', '&', $_SERVER['QUERY_STRING']);
	} else {
		$base_uri .= '?' . http_build_query($_GET);
	}
} else {
	$base_uri = $base_host;	// maybe better than nothing
}

if (strpos($base_uri, $tikiroot . 'route.php') !== false && !empty($inclusion)) {
	$base_uri = $base_url . $inclusion;
	if (!empty($_GET)) {
		$base_uri .= '?' . http_build_query($_GET, '', '&');
	}
	global $section, $sections;
	include_once('tiki-sefurl.php');
	if (isset($sections[$section]['objectType'])) {
		$objectType = $sections[$section]['objectType'];
	} else {
		$objectType = $section;
	}
	if ($objectType === 'wiki page') {
		$objectType = 'wiki';
	}

	$base_uri =  TikiLib::tikiUrlOpt(filter_out_sefurl($base_uri, $objectType));
}

// SSL options

if ( isset($_REQUEST['stay_in_ssl_mode_present']) || isset($_REQUEST['stay_in_ssl_mode']) ) {
	// We stay in HTTPS / SSL mode if 'stay_in_ssl_mode' has an 'y' or 'on' value
	$stay_in_ssl_mode = ( 
			(isset($_REQUEST['stay_in_ssl_mode']) && $_REQUEST['stay_in_ssl_mode'] == 'y') 
			|| (isset($_REQUEST['stay_in_ssl_mode']) && $_REQUEST['stay_in_ssl_mode'] == 'on' ) 
			) 
			? 'y' 
			: 'n';
} else {
	// Set default value of 'stay_in_ssl_mode' to the current mode state
	$stay_in_ssl_mode = $https_mode ? 'y' : 'n';
}

// Show the 'Stay in SSL mode' checkbox only if we are already in HTTPS
$show_stay_in_ssl_mode = $https_mode || $prefs['https_login'] == 'required' ? 'y' : 'n';
