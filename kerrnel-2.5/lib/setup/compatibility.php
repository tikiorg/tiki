<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

// for PHP < 4.2.0
if ( ! function_exists('array_fill') ) require_once('lib/compat/array_fill.func.php');

// deal with old request globals
if ( false ) { // if pre-PHP 4.1 compatibility is not required
	unset($GLOBALS['HTTP_GET_VARS']);
	unset($GLOBALS['HTTP_POST_VARS']);
	unset($GLOBALS['HTTP_COOKIE_VARS']);
	unset($GLOBALS['HTTP_ENV_VARS']);
	unset($GLOBALS['HTTP_SERVER_VARS']);
	unset($GLOBALS['HTTP_SESSION_VARS']);
	unset($GLOBALS['HTTP_POST_FILES']);
} else {
	$GLOBALS['HTTP_GET_VARS'] =& $_GET;
	$GLOBALS['HTTP_POST_VARS'] =& $_POST;
	$GLOBALS['HTTP_COOKIE_VARS'] =& $_COOKIE;
}

