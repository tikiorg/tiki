<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20100507_flash_banner_tiki.php 29782 2010-10-04 17:13:51Z sylvieg $

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Prefs replaced (and removed) by this update:
 * 	feature_sitelogo
 * 	feature_site_login
 * 	feature_top_bar
 * 	feature_sitemenu
 *  feature_sitesearch
 */

function upgrade_20101230_create_top_modules_tiki( $installer ) {
	
	// set up prefs array only
	include_once 'lib/setup/prefs.php';
	global $prefs;
	
	// add site logo
	if( $prefs['feature_sitelogo'] === 'y' ) {
		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
								"('logo','t',1,7200,'nobox=y&notitle=n&style=float%3Aleft%3B','a:1:{i:0;s:9:\"Anonymous\";}');");
	}
	// add site login
	if( $prefs['feature_site_login'] === 'y' ) {
		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
								"('login_box','t',2,0,'mode=header&nobox=y&style=float%3Aright%3B','a:1:{i:0;s:9:\"Anonymous\";}');");
	}
	// deal with top bar
	if ( $prefs['feature_top_bar'] === 'y') {
		// main site menu
		if ($prefs['feature_sitemenu'] === 'y') {
			$menuId = $installer->getOne( "SELECT `value` FROM `tiki_preferences` WHERE `name` = 'feature_topbar_id_menu'");
			$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('menu','t',5,7200,'id=$menuId&type=horiz&css=y&nobox=y&style=clear%3Aboth%3B&class=main-menu','a:1:{i:0;s:9:\"Anonymous\";}');");
		}
		// add site search
		if($prefs['feature_sitesearch'] === 'y' ) {
			$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('search','t',6,7200,'nobox=y&style=float%3Aright%3Bclear%3Aright%3B','a:1:{i:0;s:9:\"Anonymous\";}');");
		}
	}
	// add quickadmin but prefs feature_sitemycode, sitemycode stay and will need manual upgrading
	if( $prefs['feature_sitemycode'] === 'y' ) {
		$sitemycode = $installer->getOne( "SELECT `value` FROM `tiki_preferences` WHERE `name` = 'sitemycode'");
		if (strpos($sitemycode, 'quickadmin') !== false) {
			$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('quickadmin','t',3,7200,'nobox=y&style=float%3Aleft%3Bmargin%3A0+5em%3B','a:1:{i:0;s:6:\"Admins\";}');");
		}
	}
	// add breadcrumb module - feature_breadcrumbs stays for now
	if( $prefs['feature_breadcrumbs'] === 'y' ) {
		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
								"('breadcrumbs','t',6,0,'nobox=y','a:1:{i:0;s:9:\"Anonymous\";}');");
	}

//	TODO uncomment when stable (pre Tiki 7 release)
//	$installer->query( "DELETE FROM `tiki_preferences` WHERE `name` IN ".
//							"('feature_top_bar','feature_sitelogo','feature_site_login','feature_sitemenu',".
//							"'feature_topbar_id_menu','feature_sitesearch');");
	
}



