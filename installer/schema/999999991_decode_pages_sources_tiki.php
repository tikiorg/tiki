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
	set_time_limit(60 * 60); //1 hours
	include_once('tiki-setup_base.php');
	include_once ('lib/categories/categlib.php');	// needed for cat_jail fn in list_pages()
	include_once('lib/wiki/wikilib.php');

	$converter = new convertPagesToTiki9();
	$converter->convertPages();
}

