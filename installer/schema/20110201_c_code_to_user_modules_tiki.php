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
 *  feature_sitemycode
 *  sitemycode
 *  feature_secondary_sitemenu_custom_code
 *  feature_sitemenu_custom_code
 *  feature_custom_center_column_header
 *  bot_logo_code
 */

function upgrade_20110201_c_code_to_user_modules_tiki( $installer ) {
	
	// set up prefs array only
	global $prefs, $user_overrider_prefs;
	include_once 'lib/setup/prefs.php';
	$defaults = get_default_prefs();
	
	// add quickadmin but prefs feature_sitemycode, sitemycode stay and will need manual upgrading
	if( $prefs['feature_sitemycode'] === 'y' ) {
		$custom_code = $installer->getOne( "SELECT `value` FROM `tiki_preferences` WHERE `name` = 'sitemycode'");
		
		if (preg_replace('/\s/', '', $custom_code) != preg_replace('/\s/', '', $defaults['sitemycode'])) {	// line ends seem to differ
			
			$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'sitemycode', '', $custom_code, NULL));

			$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('sitemycode','t',1,7200,'nobox=y','a:0:{}');");
		}
	}
	
	if( !empty($prefs['feature_secondary_sitemenu_custom_code']) ) {
		$custom_code = $installer->getOne( "SELECT `value` FROM `tiki_preferences` WHERE `name` = 'feature_secondary_sitemenu_custom_code'");
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'secondary_sitemenu_custom_code', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('secondary_sitemenu_custom_code','t',1,7200,'nobox=y','a:0:{}');");
	}
	
	if( !empty($prefs['feature_sitemenu_custom_code']) ) {
		$custom_code = $installer->getOne( "SELECT `value` FROM `tiki_preferences` WHERE `name` = 'feature_sitemenu_custom_code'");
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'sitemenu_custom_code', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('sitemenu_custom_code','o',1,7200,'nobox=y','a:0:{}');");
	}
	
	if( !empty($prefs['feature_topbar_custom_code']) ) {
		$custom_code = $installer->getOne( "SELECT `value` FROM `tiki_preferences` WHERE `name` = 'feature_topbar_custom_code'");
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'topbar_custom_code', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('topbar_custom_code','o',1,7200,'nobox=y','a:0:{}');");
	}
	
	if( !empty($prefs['feature_custom_center_column_header']) ) {
		$custom_code = $installer->getOne( "SELECT `value` FROM `tiki_preferences` WHERE `name` = 'feature_custom_center_column_header'");
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'custom_center_column_header', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('custom_center_column_header','p',1,7200,'nobox=y','a:0:{}');");
	}
	
	if( !empty($prefs['bot_logo_code']) ) {
		$custom_code = $installer->getOne( "SELECT `value` FROM `tiki_preferences` WHERE `name` = 'bot_logo_code'");
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'bot_logo_code', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('bot_logo_code','b',1,7200,'nobox=y','a:0:{}');");
	}
	
	

//	TODO uncomment when stable (pre Tiki 7 release)
//	$installer->query( "DELETE FROM `tiki_preferences` WHERE `name` IN ".
//						"('feature_sitemycode','sitemycode', 'feature_secondary_sitemenu_custom_code',
//							'feature_sitemenu_custom_code', 'feature_custom_center_column_header', 'bot_logo_code');");
	
}



