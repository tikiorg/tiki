<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_cms.php,v 1.18.2.1 2007-12-14 16:42:07 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["cmsfeatures"])) {
	check_ticket('admin-inc-cms');
	$pref_toggles = array(
	'feature_submissions',
	'feature_cms_rankings',
	'cms_spellcheck',
	'feature_article_comments',
	'feature_cms_templates',
	'feature_cms_print',
	'feature_cms_emails'
	);
	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
}

if (isset($_REQUEST["cmsprefs"])) {
	check_ticket('admin-inc-cms');
	simple_set_value ("maxArticles");
	simple_set_value('art_home_title');
}

if (isset($_REQUEST["articlecomprefs"])) {
	check_ticket('admin-inc-cms');
  simple_set_value ("article_comments_per_page");
  simple_set_value ("article_comments_default_ordering");
}

if (isset($_REQUEST['artlist'])) {
	check_ticket('admin-inc-cms');
	$pref_toggles = array(
	'art_list_title',
	'art_list_type',
	'art_list_topic',
	'art_list_date',
	'art_list_expire',
	'art_list_visible',
	'art_list_author',
	'art_list_reads',
	'art_list_size',
	'art_list_img'
	);
	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}
	simple_set_int("art_list_title_len");
}
if (isset($_REQUEST['import'])) {
	global $artlib; include_once('lib/articles/artlib.php');
	check_ticket('admin-inc-cms');
	$fname = $_FILES['csvlist']['tmp_name'];
	$msgs = array();
	$artlib->import_csv($fname, $msgs);
	if (!empty($msgs)) {
		print_r($msgs);
		$smarty->assign_by_ref('msgs', $msgs);
	}
}
ask_ticket('admin-inc-cms');
?>
