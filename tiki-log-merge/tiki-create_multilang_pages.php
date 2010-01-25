<?php
// $Id: tiki-quick_create_multilingual_page.php 24605 2010-01-22 18:24:30Z alain_desilets $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// Initialization

include_once('tiki-setup.php');
include_once('lib/tikilib.php');
include_once('lib/wiki/wikilib.php');
include_once 'lib/wiki/semanticlib.php';
include_once ('lib/multilingual/multilinguallib.php');

make_sure_prerequisite_features_are_enabled();
make_sure_user_has_sufficient_privileges();
compute_relevant_languages();
create_pages_if_necessary();
display();

function create_pages_if_necessary() {
	global $smarty, $_REQUEST;
	$template_name = null;
	if (isset($_REQUEST['template_name'])) {
		$template_name = $_REQUEST['template_name'];	
	}
	$pages_to_create = get_pages_to_create();
	list($inexistant_page, $existing_pages)
	   = check_for_existence_of_pages($pages_to_create);
	$smarty->assign('pages_created', $inexistant_page);
	$smarty->assign('pages_not_created', $existing_pages);
	if (count($inexistant_page) > 0 || count($existing_pages) > 0) {
		$smarty->assign('display_creation_result', 'y');
	} else {
		$smarty->assign('display_creation_result', 'n');
	}
	foreach ($inexistant_page as $lang => $page_name) {
		create_page($page_name, $lang, $template_name);
	}
	make_pages_translations_of_each_other($inexistant_page);
}

function create_page($page_name, $lang, $template_name=null) {
	global $tikilib, $multilinguallib, $user;

	$content = '';
	if ($template_name != null) {
		$template_id = $multilinguallib->getTemplateIDInLanguage('wiki', $template_name, $lang);
		$template_data = $templateslib->get_template($template_id, $lang);
		$content = $template_data['content'];
	}
	$tikilib->create_page($page_name, 0, $content, null, '', null, $user, '', $lang);
}

function make_pages_translations_of_each_other($pages) {
	global $tikilib, $multilinguallib;
	if (count($pages) == 0) return;
	$first_page_id = null;
	foreach ($pages as $this_page_lang => $this_page_name) {
		if ($first_page_id == null) {
			$first_page_id = $tikilib->get_page_id_from_name($this_page_name);
			$first_page_lang = $this_page_lang;
		} else {
			$this_page_info = $tikilib->get_page_info( $this_page_name );
			$this_page_id = $this_page_info['page_id'];
			$multilinguallib->insertTranslation( 'wiki page', $first_page_id, $first_page_lang, $this_page_id, $this_page_lang );
		}
	}
}

function compute_relevant_languages() {
	global $multilinguallib, $smarty, $_REQUEST, $prefs;
	
	$all_languages_with_country_codes = $prefs['available_languages'];
	$all_languages = strip_country_code_from_lang_ids($all_languages_with_country_codes);	
	$user_languages_with_country_codes = $multilinguallib->preferredLangs();
	$user_languages = strip_country_code_from_lang_ids($user_languages_with_country_codes);
	
	$other_languages = array();
	foreach ($all_languages as $index => $lang) {
		if (!in_array($lang, $user_languages)) {
			$other_languages[] = $lang;
		}
	}
	$smarty->assign('user_languages', $user_languages);
	$smarty->assign('other_languages', $other_languages);
	
	$result = array($user_languages, $other_languages, $all_languages);
	return $result;
}

function get_pages_to_create() {
	global $_REQUEST;
	$pages_to_create = array();
	foreach ($_REQUEST as $arg_name => $arg_val) {
		if (preg_match('/page_name_([\s\S]*)/', $arg_name, $matches)) {
			if ($arg_val != '') {
				$lang = $matches[1];
				$pages_to_create[$lang] = $arg_val;
			}			
		}
	}
	set_smarty_page_links($pages_to_create);
	return $pages_to_create;
}

function check_for_existence_of_pages($pages_to_create) {
	global $tikilib, $semanticlib;
	$non_existant_pages = array();
	$existing_pages = array();
	
	foreach ($pages_to_create as $lang => $page_name) {		
		$exists = false;
		if ($tikilib->page_exists($page_name)) {
			$exists = true;
		}  else {
			$aliases = $semanticlib->getAliasContaining($page_name, true);
			if (count($aliases) > 0) {
				$exists = true;
			}
		}
		
		if ($exists) {
			$existing_pages[$lang] = $page_name;
		} else {
			$non_existant_pages[$lang] = $page_name;
		}		
	}
	
	return array($non_existant_pages, $existing_pages);
}

function make_sure_prerequisite_features_are_enabled() {
	check_this_feature('feature_wiki', 'tiki-admin.php?page=features');
	check_this_feature('feature_multilingual', 'tiki-admin.php?page=features');
}

function set_smarty_page_links($page_names) {
	global $wikilib, $smarty;
	
	$page_links = array();
	foreach ($page_names as $a_page_name) {
		$page_links[$a_page_name] =
			"<a href=\"".
			$wikilib->url_for_operation_on_a_page('tiki-index.php', $a_page_name, false).
			"\">$a_page_name</a>";
	}
	$smarty->assign('page_links', $page_links);
}	

function check_this_feature($feature_name, $enabling_url) {
	global $prefs, $smarty;	
	if (!isset($prefs[$feature_name]) || $prefs[$feature_name] != 'y') {
		$smarty->assign('msg', 
			tra('This feature is disabled').
			": $feature_name"."<P>\n".
			tra('To enable this feature, go to:').
			" <a href=\"$enabling_url\">".tra('Admin Feature').'</a>. '.tra('If you do not have privileges to activate this feature, ask the site admin to do it.').'</a>');
		$smarty->display('error.tpl');
		die;
	}	
}

function display() {
	global $smarty;
	// disallow robots to index page:
	$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
	$smarty->assign('mid', 'tiki-create_multilang_pages.tpl');
	$smarty->display('tiki.tpl');
}

function strip_country_code_from_lang_ids($lang_ids_with_country_code) {
	$lang_ids = array();
	foreach ($lang_ids_with_country_code as $index => $this_lang_id) {
		if (strlen($this_lang_id) > 2) {
			preg_match('/^([^\-]*)-/', $this_lang_id, $matches);
			$this_lang_id = $matches[1];

		}
		if (!in_array($this_lang_id, $lang_ids)) {
			$lang_ids[] = $this_lang_id;
		}
	}
	
	return $lang_ids;
}

function make_sure_user_has_sufficient_privileges() {
	global $user;
	// TODO: Figure out how to make sure that user has page creation permissions.
}