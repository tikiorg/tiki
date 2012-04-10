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

/* ABOUT THE NUMBERING:
 *
 * Because this script calls tiki-setup_base.php , which does very
 * complicated things like checking if users are logged in and so
 * on, this script depends on every other script, because
 * tiki-setup_base.php does.
 *
 * -----
 *
 * Second attempt at decoding entities but only in plugin args instead of whole pages as in old version of this script (pre r40858)
 * 				(see http://info.tiki.org/HTMLentities for an example)
 *
 * Sometimes entities had been "double" encoded so "&quot;" appeared as "&amp;quot;" - seems mostly in plugin args?
 * 		(leaving others alone so entities that were supposed to appear still do)
 *
 * As seen here: https://trunkinfo.tiki.org/tiki-pagehistory.php?page=ダウンロード&history_offset=1&diff_style=sidediff&show_all_versions=y&compare=Compare&newver=5&oldver=4&bothver_idx=4
 */



function upgrade_999999991_decode_pages_sources_tiki($installer)
{
	global $tikilib, $prefs, $tikiroot, $user_overrider_prefs, $tiki_p_trust_input, $smarty, $access, $local_php, $categlib, $headerlib;	// globals are required here for tiki-setup_base.php
	include_once('tiki-setup_base.php');
	include_once ('lib/categories/categlib.php');	// needed for cat_jail fn in list_pages()

	$maxRecords = 100;
	// The outer loop attemps to limit memory usage by fetching pages gradually
	for ($offset = 0; $pages = $tikilib->list_pages($offset, $maxRecords), !empty($pages['data']); $offset += $maxRecords) {
		foreach ($pages['data'] as $page) {
			if (!$page['is_html']) {

				$data = $page['data'];
				$parserlib = TikiLib::lib('parser');

				$matches = WikiParser_PluginMatcher::match($data);			// find the plugins

				$replaced = array();

				foreach ($matches as $match) {								// each plugin
					$plugin = (string) $match;
					$key = '§'.md5($tikilib->genPass()).'§';				// by replace whole plugin with a guid
					$data = preg_replace('/' . preg_quote($plugin, '/') . '/', $key, $data);

					$body = $match->getBody();									// leave the bodies alone
					$key2 = '§'.md5($tikilib->genPass()).'§';					// by replacing it with a guid
					$plugin = preg_replace('/' . preg_quote($body, '/') . '/', $key2, $plugin);
					$plugin = str_replace('&amp;', '&', $plugin);				// remove &amp; in case of double encoding as seen on info.t.o
					$plugin = htmlspecialchars_decode($plugin);					// decode entities in the plugin args (usually &quot;)

					$plugin = str_replace($key2, $body, $plugin);				// finally put the body back

					$replaced['key'][] = $key;
					$replaced['data'][] = $plugin;						// store the decoded-args plugin for replacement later

				}

				$parserlib->plugins_replace($data, $replaced);			// put the plugins back into the page


				if ($data != $page['data']) {
					$tikilib->update_page(
									$page['pageName'],
									$data,
									'System upgrade: Converting HTML characters in plugin args (v3)',
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

