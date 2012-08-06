<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/* Automatically set params used for absolute URLs - BEGIN */

// Note: need to substitute \ for / for windows.
$tikipath = str_replace('\\', '/', realpath(dirname(__FILE__)));

if (getcwd()) {
	$scriptDirectory = getcwd();
} else {
	// On some systems, SCRIPT_FILENAME contains the full path to the cgi script
	// that calls the script we are looking for. In this case, we have to
	// fallback to PATH_TRANSLATED. This one may be wrong on some systems, this
	// is why SCRIPT_FILENAME is tried first.
	if ( substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($tiki_setup_dir)) != $tikipath ) {
		// PATH_TRANSLATED is not always set on PHP5, so try to get first value of get_included_files() in this case	
		$scriptDirectory = empty($_SERVER['PATH_TRANSLATED']) ? current(get_included_files()) : $_SERVER['PATH_TRANSLATED'];
	} else {
		$scriptDirectory = $_SERVER['SCRIPT_FILENAME'];
	}
	$scriptDirectory = dirname(realpath($scriptDirectory));
}
// Note: need to substitute \ for / for Windows.
$scriptDirectory = str_replace('\\', '/', $scriptDirectory);

$dir_level = substr_count(str_replace($tikipath, '', $scriptDirectory), "/");

// If unallowed chars (regarding to RFC1738) have been found in REQUEST_URI, then urlencode them
$unallowed_uri_chars = array("'", '"', '<', '>', '{', '}', '|', '\\', '^', '~', '`');
$unallowed_uri_chars_encoded = array_map('urlencode', $unallowed_uri_chars);
if (isset($_SERVER['REQUEST_URI']))
	$_SERVER['REQUEST_URI'] = str_replace($unallowed_uri_chars, $unallowed_uri_chars_encoded, $_SERVER['REQUEST_URI']);

// Same as above, but for PHP_SELF which does not contain URL params
// Usually, PHP_SELF also differs from REQUEST_URI in that PHP_SELF is URL decoded and REQUEST_URI is exactly what the client sent
$unallowed_uri_chars = array_merge($unallowed_uri_chars, array('#', '[', ']'));
$unallowed_uri_chars_encoded = array_merge($unallowed_uri_chars_encoded, array_map('urlencode', array('#', '[', ']')));
$_SERVER['SCRIPT_NAME'] = str_replace($unallowed_uri_chars, $unallowed_uri_chars_encoded, $_SERVER['SCRIPT_NAME']);

// Note: need to substitute \ for / for Windows.
$tikiroot = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

if ($dir_level > 0) {
	$tikiroot = preg_replace('#(/[^/]+){'.$dir_level.'}$#', '', $tikiroot);
	chdir($tikipath);
}

if ( substr($tikiroot, -1, 1) != '/' ) $tikiroot .= '/';
if ( substr($tikipath, -1, 1) != '/' ) $tikipath .= '/';

// Add global filter for xajax and cookie	// AJAX_TODO?
global $inputConfiguration;
if ( empty($inputConfiguration) ) {
	$inputConfiguration = array();
}
array_unshift(
				$inputConfiguration, array(
				  'staticKeyFilters' => array(
						'cookietab'	=>	'int',
						'callback'  => 'word',
					),
					'staticKeyFiltersForArrays' => array(
					)
				)
);

require_once('lib/init/initlib.php');
TikiInit::prependIncludePath($tikipath.'lib/pear');
TikiInit::appendIncludePath($tikipath.'lib/core');
TikiInit::appendIncludePath($tikipath);
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()
	->registerNamespace('Horde')
	->registerNamespace('TikiFilter')
	->registerNamespace('DeclFilter')
	->registerNamespace('JisonParser')
	->registerNamespace('JitFilter')
	->registerNamespace('Search')
	->registerNamespace('Perms')
	->registerNamespace('Math')
	->registerNamespace('Category')
	->registerNamespace('WikiParser')
	->registerNamespace('StandardAnalyzer')
	->registerNamespace('Tracker')
	->registerNamespace('Event_')
	->registerNamespace('Services_')
	->registerNamespace('Tiki_')
	->registerNamespace('TikiDb')
	->registerNamespace('Report')
	->registerNamespace('Feed')
	->registerNamespace('FileGallery')
	->registerNamespace('WikiPlugin');
