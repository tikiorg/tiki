<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Prefs replaced (and removed) by this update:
 *  feature_sitemycode
 *  sitemycode
 *  sitemycode_publish
 *  feature_secondary_sitemenu_custom_code
 *  feature_sitemenu_custom_code
 *  feature_custom_center_column_header
 *  bot_logo_code
 *  feature_bot_logo
 *  feature_topbar_custom_code
 */

function upgrade_20110201_c_code_to_user_modules_tiki( $installer ) {
	
	// set up prefs array only
	global $prefs, $user_overrider_prefs;
	include_once 'lib/setup/prefs.php';

	$defaultsitemycode = '{if $tiki_p_admin == "y"}
<div id="quickadmin" style="text-align: left; padding-left: 12px;"><small>{tr}Quick Admin{/tr}</small>:
{icon _id=database_refresh title="{tr}Clear all Tiki caches{/tr}" href="tiki-admin_system.php?do=all"}
{icon _id=wrench title="{tr}Modify the look &amp; feel (logo, theme, etc.){/tr}" href="tiki-admin.php?page=look&amp;cookietab=2"} 
{if $prefs.lang_use_db eq "y"}{icon _id=world_edit title="{tr}Show interactive translation settings{/tr}" href="tiki-edit_languages.php?interactive_translation_mode=on"}{/if}
</div>  
{/if}';

	$prefs = array_merge( array(	// merge in relevant defaults from 6.x as they are no longer defined in 7.x+
		'feature_sitemycode' => 'y',
		'sitemycode' => $defaultsitemycode,
		'sitemycode_publish' => 'n',
		'feature_secondary_sitemenu_custom_code' => '',
		'feature_sitemenu_custom_code' => '',
		'feature_topbar_custom_code' => '',
		'feature_custom_center_column_header' => '',
		'bot_logo_code' => '',
		'feature_bot_logo' => 'n',
	), $prefs);
	
	// add quickadmin but prefs feature_sitemycode, sitemycode stay and will need manual upgrading
	if( $prefs['feature_sitemycode'] === 'y' ) {
		$custom_code = $prefs['sitemycode'];
		
		if (preg_replace('/\s/', '', $custom_code) != preg_replace('/\s/', '', $defaultsitemycode)) {	// line ends seem to differ
			
			$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'sitemycode', '', $custom_code, NULL));

			if ($prefs['sitemycode_publish'] === 'y') {
				$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
										"('sitemycode','t',1,7200,'nobox=y','a:0:{}');");
			}
		}
	}
	
	if( !empty($prefs['feature_secondary_sitemenu_custom_code']) ) {
		$custom_code = $prefs['feature_secondary_sitemenu_custom_code'];
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'secondary_sitemenu_custom_code', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('secondary_sitemenu_custom_code','t',1,7200,'nobox=y','a:0:{}');");
	}
	
	if( !empty($prefs['feature_sitemenu_custom_code']) ) {
		$custom_code = $prefs['feature_sitemenu_custom_code'];
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'sitemenu_custom_code', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('sitemenu_custom_code','o',1,7200,'nobox=y','a:0:{}');");
	}
	
	if( !empty($prefs['feature_topbar_custom_code']) ) {
		$custom_code = $prefs['feature_topbar_custom_code'];
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'topbar_custom_code', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('topbar_custom_code','o',1,7200,'nobox=y','a:0:{}');");
	}
	
	if( !empty($prefs['feature_custom_center_column_header']) ) {
		$custom_code = $prefs['feature_custom_center_column_header'];
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'custom_center_column_header', '', $custom_code, NULL));

		$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
									"('custom_center_column_header','p',1,7200,'nobox=y','a:0:{}');");
	}
	
	if( !empty($prefs['bot_logo_code']) ) {
		$custom_code = $prefs['bot_logo_code'];
		
		$installer->query( "INSERT INTO `tiki_user_modules` (name,title,data,parse) VALUES (?,?,?,?);",
					array( 'bot_logo_code', '', $custom_code, NULL));

		if ($prefs['feature_bot_logo'] === 'y') {
			$installer->query( "INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES ".
										"('bot_logo_code','b',1,7200,'nobox=y','a:0:{}');");
		}
	}
	
	

//	TODO uncomment when stable (pre Tiki 7 release)
//	$installer->query( "DELETE FROM `tiki_preferences` WHERE `name` IN ".
//						"('feature_sitemycode','sitemycode', 'sitemycode_publish', 'feature_secondary_sitemenu_custom_code',
//							'feature_sitemenu_custom_code', 'feature_custom_center_column_header',
//							'bot_logo_code', 'feature_bot_logo', 'feature_topbar_custom_code');");
	
}



