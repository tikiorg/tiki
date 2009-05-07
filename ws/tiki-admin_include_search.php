<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_search.php,v 1.21.2.2 2008-03-20 16:12:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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
	simple_set_toggle("feature_search_show_object_filter");
	simple_set_toggle("feature_search_show_search_box");
	simple_set_toggle("feature_search_show_visit_count");
	simple_set_toggle("feature_search_show_pertinence");
	simple_set_toggle("feature_search_show_object_type");
	simple_set_toggle("feature_search_show_last_modification");
	simple_set_int("search_refresh_rate");
	simple_set_int("search_min_wordlength");
	simple_set_int("search_max_syllwords");
	simple_set_int("search_syll_age");
	simple_set_int("search_lru_purge_rate");
	simple_set_int("search_lru_length");
	simple_set_value("search_refresh_index_mode");
	simple_set_toggle("search_parsed_snippet");
}

function activated_features() {
	global $prefs;
	$activated_features = array();
	$features = array('articles','blogs','directory','comments','faqs','file_galleries','forums','wiki','trackers','galleries');
	foreach($features as $feature) {
		switch ($feature) {
			case 'blogs':
				if (isset($prefs['feature_blogs']) and $prefs['feature_blogs'] == 'y') {
					$activated_features[] = 'blogs';
					$activated_features[] = 'blogs_posts';
				}
				break;
			case 'comments':
				$activated_features[] = 'comments';
				break;
			case 'wiki':
				if (isset($prefs['feature_wiki']) and $prefs['feature_wiki'] == 'y') {
					$activated_features[] = 'pages';
				}
				break;
			case 'articles':
				if (isset($prefs['feature_articles']) and $prefs['feature_articles'] == 'y') {
					$activated_features[] = 'articles';
				}
				break;
			case 'faqs':
				if (isset($prefs['feature_faqs']) and $prefs['feature_faqs'] == 'y') {
					$activated_features[] = 'faqs';
					$activated_features[] = 'faqs_questions';
				}
				break;
			case 'file_galleries':
				if (isset($prefs['feature_file_galleries']) and $prefs['feature_file_galleries'] == 'y') {
					$activated_features[] = 'file_galleries';
					$activated_features[] = 'files';
				}
				break;
			case 'forums':
				if (isset($prefs['feature_forums']) and $prefs['feature_forums'] == 'y') {
					$activated_features[] = 'forums';
				}
				break;
			case 'galleries':
				if (isset($prefs['feature_galleries']) and $prefs['feature_galleries'] == 'y') {
					$activated_features[] = 'galleries';
					$activated_features[] = 'images';
				}
				break;
			case 'trackers':
				if (isset($prefs['feature_trackers']) and $prefs['feature_trackers'] == 'y') {
					$activated_features[] = 'trackers';
					$activated_features[] = 'tracker_items';
				}
				break;
			case 'directory':
				if (isset($prefs['feature_directory']) and $prefs['feature_directory'] == 'y') {
					$activated_features[] = 'directory_categories';
					$activated_features[] = 'directory_sites';
				}
				break;
		}
	}
	return $activated_features;
}

global $tiki_p_admin;
if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_index_all_now']) && $_REQUEST['refresh_index_all_now'] == 'y') {
  	require_once('lib/search/refresh-functions.php');
	foreach(activated_features() as $feature)
		refresh_index($feature);
	$smarty->assign('refresh_index_all_now', $_REQUEST['refresh_index_all_now']);
}
if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_files_index_now']) && $_REQUEST['refresh_files_index_now'] == 'y') {
  	require_once('lib/search/refresh-functions.php');
	refresh_index('files');
	$smarty->assign('refresh_files_index_now', $_REQUEST['refresh_files_index_now']);
}
if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_index_now']) && $_REQUEST['refresh_index_now'] == 'y') {
  	require_once('lib/search/refresh-functions.php');
	refresh_index('pages');
	$smarty->assign('refresh_index_now', $_REQUEST['refresh_index_now']);
}
if ($tiki_p_admin == 'y' && !empty($_REQUEST['refresh_tracker_index_now']) && $_REQUEST['refresh_tracker_index_now'] == 'y') {
  	require_once('lib/search/refresh-functions.php');
	refresh_index('tracker_items');
	$smarty->assign('refresh_tracker_index_now', $_REQUEST['refresh_tracker_index_now']);
}
$headerlib->add_cssfile('css/admin.css');

ask_ticket('admin-inc-search');
?>
