<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* ABOUT THE NUMBERING:
 *
 * Because this script calls tiki-setup_base.php , which does very
 * complicated things like checking if users are logged in and so
 * on, this script depends on every other script, because
 * tiki-setup_base.php does.
 *
 * -----
 *
 * 				see http://info.tiki.org/HTMLentities for examples of HTML entities
 */


/**
 * @param $installer
 */
function upgrade_999999991_decode_pages_sources_tiki($installer)
{
	global $user_overrider_prefs, $systemConfiguration;
	set_time_limit(60 * 60); // Set maximum execution time to 1 hour since this runs on all pages
	include_once('tiki-setup.php');
	$categlib = TikiLib::lib('categ');
	$wikilib = TikiLib::lib('wiki');

	$converter = new convertToTiki9();
	$converter->convertPages();
	$converter->convertModules();
}

