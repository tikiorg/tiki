<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * Tikiwiki CATORPHANS plugin.
 * 
 * Syntax:
 * 
 * {CATORPHANS(
 *			objects=>wiki		#types of objects to display; defaults to 'wiki'
 *         )}
 * {CATORPHANS}
 * 
 * Currently only displays wiki pages; very much a work in progress
 */
function wikiplugin_catorphans_help() {
	return tra("Display Tiki objects that have not been categorized").":<br />~np~{CATORPHANS(objects=>wiki|article|blog|faq|fgal|forum|igal|newsletter|poll|quizz|survey|tracker)}{CATORPHANS}~/np~";
}

function wikiplugin_catorphans_info() {
	return array(
		'name' => tra('Category Orphans'),
		'documentation' => tra('PluginCatOrphans'),
		'description' => tra('Display wiki pages that have not been categorized'),
		'prefs' => array( 'feature_categories', 'wikiplugin_catorphans' ),
		'params' => array(
			'objects' => array(
				'required' => false,
				'name' => tra('Object'),
				'description' => tra('Currently, only works with wiki pages (set to wiki (Wiki Pages) by default)'),
				'default' => 'wiki',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Wiki Pages'), 'value' => 'wiki'),
				) 
			),
			'max' => array(
				'required' => false,
				'name' => tra('Max'),
				'description' => tra('Maximum number of items. Use -1 for unlimited. Default is the site admin setting for maximum records.'),
				'default' => '$prefs[\'maxRecords\']'
			),
			'offset' => array(
				'required' => false,
				'name' => tra('Result Offset'),
				'description' => tra('Result number at which the listing should start (default is no offset)'),
				'default' => 0
			),
		),
	);
}

function wikiplugin_catorphans($data, $params) {
	global $dbTiki, $smarty, $tikilib, $prefs, $access;
	$access->check_feature('feature_categories');
	global $categlib; require_once ('lib/categories/categlib.php');

	$default = array('offset'=>0, 'max'=>$prefs['maxRecords'], 'objects'=>'wiki');
	$params = array_merge($default, $params);
	extract ($params,EXTR_SKIP);

	// array for converting long type names (as in database) to short names (as used in plugin)
	$typetokens = array(
		"article" => "article",
		"blog" => "blog",
		"faq" => "faq",
		"file gallery" => "fgal",
		"image gallery" => "igal",
		"newsletter" => "newsletter",
		"poll" => "poll",
		"quiz" => "quiz",
		"survey" => "survey",
		"tracker" => "tracker",
		"wiki page" => "wiki"
	);

	// TODO: move this array to a lib
	// array for converting long type names to translatable headers (same strings as in application menu)
	$typetitles = array(
		"article" => "Articles",
		"blog" => "Blogs",
		"directory" => "Directory",
		"faq" => "FAQs",
		"file gallery" => "File Galleries",
		"forum" => "Forums",
		"image gallery" => "Image Gals",
		"newsletter" => "Newsletters",
		"poll" => "Polls",
		"quiz" => "Quizzes",
		"survey" => "Surveys",
		"tracker" => "Trackers",
		"wiki page" => "Wiki"
	);
	if (!empty($_REQUEST['offset'])) {
		$offset = $_REQUEST['offset'];
	}

	// currently only supports display of wiki pages
	if ($objects == 'wiki') {
		$listpages = $tikilib->list_pages($offset, $max, 'pageName_asc', '', '', true, true, false, false, array('noCateg' => true));
		$smarty->assign_by_ref('pages', $listpages['data']);
		$smarty->assign('pagination', array('cant'=>$listpages['cant'], 'step'=>$max, 'offset'=>$offset));
		return '~np~'.$smarty->fetch('wiki-plugins/wikiplugin_catorphans.tpl').'~/np~';		
	}
	return '';

}
