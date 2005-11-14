<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_search.php,v 1.17 2005-11-14 22:22:39 sylvieg Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


if (isset($_REQUEST["searchprefs"])) {
	check_ticket('admin-inc-search');
	simple_set_toggle("feature_search_fulltext");
	simple_set_toggle("feature_search_stats");
	simple_set_toggle("feature_referer_highlight");
	simple_set_toggle("feature_search_show_forbidden_obj");
	simple_set_toggle("feature_search_show_forbidden_cat");
	simple_set_int("search_refresh_rate");
	simple_set_int("search_min_wordlength");
	simple_set_int("search_max_syllwords");
	simple_set_int("search_syll_age");
	simple_set_int("search_lru_purge_rate");
	simple_set_int("search_lru_length");
}

global $tiki_p_admin;
if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_index_now']) && $_REQUEST['refresh_index_now'] == 'y') {
  	require_once('lib/search/refresh-functions.php');
  	refresh_index_wiki_all();
}

if (!empty($_REQUEST['refresh_index_now'])) {
	$smarty->assign('refresh_index_now', $_REQUEST['refresh_index_now']);
}

ask_ticket('admin-inc-search');
?>
