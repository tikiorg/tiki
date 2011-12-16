<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 99999999_image_plugins_kill_tiki.php 38695 2011-11-04 06:16:12Z pkdille $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $tikilib, $prefs, $tikiroot, $user_overrider_prefs, $tiki_p_trust_input, $smarty, $access;	// globals are required here for tiki-setup_base.php
include_once('tiki-setup_base.php');

// ABOUT THE NUMBERING:
//
// Because this script calls tiki-setup_base.php , which does very
// complicated things like checking if users are logged in and so
// on, this script depends on every other script, because
// tiki-setup_base.php does.


function upgrade_999999991_decode_pages_sources_tiki($installer)
{
	global $tikilib;

	$maxRecords = 100;
	// The outer loop attemps to limit memory usage by fetching pages gradually
	for ($offset = 0; $pages = $tikilib->list_pages($offset, $maxRecords), !empty($pages['data']); $offset += $maxRecords) {
		foreach ($pages['data'] as $page) {
			if (!$page['is_html']) {
				$data = htmlspecialchars_decode($page['data']);
				if ($data != $page['data']) {
					$tikilib->update_page(
								$page['pageName'], 
								$data,
								'System upgrade: Converting special HTML characters',
								'admin',
								'0.0.0.0',
								$page['description'],
								1,
								$page['lang'], 
								$page['is_html'],
								null,
								null,
								'',
								$page['wiki_authors_style']
					);
				}
			}
		}
	}
	
}

