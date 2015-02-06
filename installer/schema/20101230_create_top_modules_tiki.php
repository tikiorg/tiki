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

/*
 * Prefs replaced (and removed) by this update:
 * 	feature_sitelogo
 * 	feature_site_login
 * 	feature_top_bar
 *  feature_topbar_id_menu
 * 	feature_sitemenu
 *  feature_sitesearch
 */

/**
 * @param $installer
 */
function upgrade_20101230_create_top_modules_tiki($installer)
{

	$prefs = array();
	$result = $installer->table('tiki_preferences')->fetchAll(array('name', 'value'), array());
	foreach ($result as $res) {
		$prefs[$res['name']] = $res['value'];
	}

// merge in relevant defaults from 6.x as they are no longer defined in 7.x+
	$prefs = array_merge(
		array(
			'feature_sitelogo' => 'y',
			'feature_site_login' => 'y',
			'feature_top_bar' => 'y',
			'feature_sitemenu' => 'n',
			'feature_sitesearch' => 'y',
			'feature_sitemycode' => 'y',
			'feature_breadcrumbs' => 'n',
			'feature_topbar_id_menu' => '42',
		),
		$prefs
	);

	// add site logo
	if ($prefs['feature_sitelogo'] === 'y') {
		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('logo','t',1,7200,'nobox=y','a:0:{}');"
		);
	}
	// add site login
	if ($prefs['feature_site_login'] === 'y') {
		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('login_box','t',2,0,'mode=popup&nobox=y','a:0:{}');"
		);
	}
	// deal with top bar
	if ($prefs['feature_top_bar'] === 'y') {
		// main site menu
		if ($prefs['feature_sitemenu'] === 'y') {
			$menuId = $prefs['feature_topbar_id_menu'];
			$installer->query(
				"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
				"('menu','o',2,7200,'id=$menuId&type=horiz&menu_id=tiki-top&menu_class=clearfix&nobox=y','a:0:{}');"
			);
		}
		// add site search
		if ($prefs['feature_sitesearch'] === 'y') {
			$installer->query(
				"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
				"('search','o',1,7200,'nobox=y','a:0:{}');"
			);
		}
	}
	// add quickadmin but prefs feature_sitemycode, sitemycode stay and will need manual upgrading
	if ($prefs['feature_sitemycode'] === 'y') {
		$sitemycode = $installer->getOne("SELECT `value` FROM `tiki_preferences` WHERE `name` = 'sitemycode'");
		if (strpos($sitemycode, 'quickadmin') !== false) {
			$installer->query(
				"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES" .
				" ('quickadmin','t',3,7200,'nobox=y','a:1:{i:0;s:6:\"Admins\";}');"
			);
		}
	}
	// add breadcrumb module - feature_breadcrumbs stays for now
	if ($prefs['feature_breadcrumbs'] === 'y') {
		$installer->query(
			"INSERT INTO `tiki_modules` (name,position,ord,cache_time,params,groups) VALUES " .
			"('breadcrumbs','t',6,0,'nobox=y','a:0:{}');"
		);
	}

	//	TODO uncomment when stable (pre Tiki 7 release)
	//	$installer->query("DELETE FROM `tiki_preferences` WHERE `name` IN ".
	//							"('feature_top_bar','feature_sitelogo','feature_site_login','feature_sitemenu',".
	//							"'feature_topbar_id_menu','feature_sitesearch');");

}
