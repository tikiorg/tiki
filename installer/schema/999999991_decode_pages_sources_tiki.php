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
	set_time_limit(60 * 60 * 3); //3 hours

	global $tikilib, $prefs, $tikiroot, $user_overrider_prefs, $tiki_p_trust_input, $smarty, $access, $local_php, $categlib, $headerlib;	// globals are required here for tiki-setup_base.php
	include_once('tiki-setup_base.php');
	$parserlib = TikiLib::lib('parser');

	include_once ('lib/categories/categlib.php');	// needed for cat_jail fn in list_pages()

	//we want to limit how much we have in memory, so here we count the pages that have plugins so we have can then offset threw them
	$ids =
		TikiLib::fetchAll('SELECT page_id FROM tiki_pages') +
		TikiLib::fetchAll('SELECT historyId FROM tiki_history');

	foreach($ids as $id) {
		if (isset($id['page_id'])) {
			$data = TikiLib::getOne("SELECT data FROM tiki_pages WHERE page_id = ?", array($id['page_id']));
		} else {
			$data = TikiLib::getOne("SELECT data FROM tiki_history WHERE historyId = ?", array($id['historyId']));
		}

		//We know the page was single encoded, but plugin possibly double?
		$data =  htmlspecialchars_decode($data);

		$matches = WikiParser_PluginMatcher::match($data);			// find the plugins

		$replaced = array();

		foreach ($matches as $match) {								// each plugin
			$plugin = (string) $match;
			$key = '§'.md5($tikilib->genPass()).'§';				// by replace whole plugin with a guid
			$data = preg_replace('/' . preg_quote($plugin, '/') . '/', $key, $data);

			$body = $match->getBody();									// leave the bodies alone
			$key2 = '§'.md5($tikilib->genPass()).'§';					// by replacing it with a guid
			$plugin = preg_replace('/' . preg_quote($body, '/') . '/', $key2, $plugin);

			//Here we detect if a plugin was double encoded and this is the second decode
			if (preg_match("/&amp;&/i", $plugin) || preg_match("/&quot;/i", $plugin)) { //try to detect double encoding
				$plugin = htmlspecialchars_decode($plugin);					// decode entities in the plugin args (usually &quot;)
			}

			$plugin = str_replace($key2, $body, $plugin);				// finally put the body back

			$replaced['key'][] = $key;
			$replaced['data'][] = $plugin;						// store the decoded-args plugin for replacement later

		}

		$parserlib->plugins_replace($data, $replaced);			// put the plugins back into the page

		if (isset($id['page_id'])) {
			TikiLib::query("UPDATE tiki_pages SET data = ? WHERE page_id = ?", array($data, $id['page_id']));
		} else {
			TikiLib::query("UPDATE tiki_history SET data = ? WHERE historyId = ?", array($data, $id['historyId']));
		}
	}
}

