<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $tikilib, $prefs, $tikiroot, $user_overrider_prefs, $tiki_p_trust_input, $smarty, $access, $local_php;	// globals are required here for tiki-setup_base.php
include_once('tiki-setup_base.php');

// ABOUT THE NUMBERING:
//
// Because this script calls tiki-setup_base.php , which does very
// complicated things like checking if users are logged in and so
// on, this script depends on every other script, because
// tiki-setup_base.php does.

/*
 * Second attempt at decoding entities left behind by 999999991_decode_pages_sources_tiki
 *
 * Sometimes entities had been "double" encoded so "&quot;" appeared as "&amp;quot;" - seems mostly in plugin args?
 * 		(leaving others alone so entities that were supposed to appear still do)
 *
 * As seens on https://trunkinfo.tiki.org/tiki-pagehistory.php?page=ダウンロード&history_offset=1&diff_style=sidediff&show_all_versions=y&compare=Compare&newver=5&oldver=4&bothver_idx=4
 */

function upgrade_999999992_decode_pages_plugin_args_tiki($installer)
{
	global $tikilib;

	$maxRecords = 100;
	// The outer loop attemps to limit memory usage by fetching pages gradually
	for ($offset = 0; $pages = $tikilib->list_pages($offset, $maxRecords), !empty($pages['data']); $offset += $maxRecords) {
		foreach ($pages['data'] as $page) {
			if (!$page['is_html']) {

				$data = $page['data'];
				$parserlib = TikiLib::lib('parser');
				$noparsed = array();
				$parserlib->plugins_remove($data, $noparsed);	// get all the plugins from the page into $noparsed

				if (is_array($noparsed['data'])) {
					foreach ($noparsed['data'] as & $plugin_source) {

						$matches = WikiParser_PluginMatcher::match($plugin_source);	// find the plugins

						foreach ($matches as $match) {								// can be nested nasties

							$body = $match->getBody();								// leave the bodies alone
							$key = '§'.md5($tikilib->genPass()).'§';				// by replacing it with a guid
							$temp = preg_replace('/' . preg_quote($body, '/') . '/', $key, $plugin_source);

							$temp = htmlspecialchars_decode($temp);					// decode entities in the plugin args (usually &quot;)

							$plugin_source = str_replace($key, $body, $temp);		// finally put the body back
						}
					}
				}

				$parserlib->plugins_replace($data, $noparsed);	// put the plugins back into the page

				if ($data != $page['data']) {
					$tikilib->update_page(
									$page['pageName'],
									$data,
									'System upgrade: Converting special HTML characters in plugin args',
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

