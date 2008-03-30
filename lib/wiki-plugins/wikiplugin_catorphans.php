<?php
/* $Id:
 *
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

function wikiplugin_catorphans($data, $params) {
	global $dbTiki, $smarty, $tikilib, $prefs, $categlib;

	if (!is_object($categlib)) {
		require_once ("lib/categories/categlib.php");
	}

	if ($prefs['feature_categories'] != 'y') {
		return "<span class='warn'>" . tra("Categories are disabled"). "</span>";
	}

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

	// default object is 'wiki'
	if (!isset($objects)or $objects != 'wiki') {
		$objects = 'wiki';
	}

	$orphans = '';

	// currently only supports display of wiki pages
	if ($objects == 'wiki') {
		$listpages = $tikilib->list_pageNames(0, -1, 'pageName_asc', '');

		foreach ($listpages['data'] as $page) {
			if (!$categlib->is_categorized('wiki page', $page['pageName'])) {
				//				$orphans .= '<a href="tiki-index.php?page='.$page['pageName'].'">'.$page['pageName'].'</a><br />';
				$orphans .= '((' . $page['pageName'] . '))<br />';
			}
		}
	}

	return $orphans;
}

?>
