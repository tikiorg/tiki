<?php

/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_category.php,v 1.4 2003-09-08 14:52:23 sylvieg Exp $
 *
 * Tiki-Wiki CATEGORY plugin.
 * 
 * Syntax:
 * 
 * {CATEGORY(
 *	id=>1+2+3,	 # defaults to current
 *	types=>article+blog+faq+fgal+igal+newsletter+poll+quiz+survey+tracker+wiki # list of types of objects, default * (all),
 *	sort=>[type|created|name|hits]_[asc|desc]	# default name_asc,
 *	sub=>true|false		# display items of subcategories # default is 'true';
 *	split=>y|n		# when displaying multiple categories, whether they should be split or not; defaults to yes
 * )}
 * {CATEGORY}
 * 
  */
function wikiplugin_category_help() {
	return tra("Insert list of items for the current/given category into wiki page").":<br />~np~{CATEGORY(id=>1+2+3,types=>article+blog+faq+fgal+igal+newsletter+poll+quiz+survey+tracker+wiki ,sort=>[type|created|name|hits]_[asc|desc],sub=>true|false,split=>y|n)}{CATEGORY}~/np~";
}

function in_multi_array($needle, $haystack) {
	$in_multi_array = false;

	if (in_array($needle, $haystack)) {
		$in_multi_array = true;
	} else {
		while (list($tmpkey, $tmpval) = each($haystack)) {
			if (is_array($haystack[$tmpkey])) {
				if (in_multi_array($needle, $haystack[$tmpkey])) {
					$in_multi_array = true;

					break;
				}
			}
		}
	}

	return $in_multi_array;
}

function wikiplugin_category($data, $params) {
	global $dbTiki;

	global $smarty;
	global $tikilib;
	global $feature_categories;
	global $categlib;

	if (!is_object($categlib)) {
		require_once ("lib/categories/categlib.php");
	}

	if ($feature_categories != 'y') {
		return "<span class='warn'>" . tra("Categories are disabled"). "</span>";
	}

	extract ($params);

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

	// string given back to caller
	$out = "";

	// TODO: use categ name instead of id (alternative)
	$id = (isset($id)) ? $id : 'current'; // use current category if none is given

	if ($id == 'current') {
		$objId = urldecode($_REQUEST['page']);

		$catids = $categlib->get_object_categories('wiki page', $objId);
	} else {
		$catids = explode("+", $id);      // create array of category ids to be displayed
	}

	// default setting for $split is 'yes'
	if (!(isset($split))) {
		$split = 'yes';
	} elseif ($split != 'y' and $split != 'yes' and $split != 'n' and $split != 'no' and $split != 'true' and $split != 'false') {
		$split = 'yes';
	}

	// array with items to be displayed
	$listcat = array();
	// title of categories
	$title = '';

	// TODO: allow 'find' and 'maxRecords'
	$find = "";
	$offset = 0;
	$maxRecords = 500;
	$count = 0;

	$sort = (isset($sort)) ? $sort : "name_asc";
	$types = (isset($types)) ? "+" . strtolower($types) : "*";

	$typesallowed = split("\+", $types); // create array of types the user allowed to be displayed

	foreach ($catids as $id) {
		// get data of category
		$cat = $categlib->get_category($id);

		// store name of category
		if ($count != 0) {
			$title .= "| <a href='tiki-browse_categories.php?parentId=" . $id . "'>" . $cat['name'] . "</a> ";
		} else {
			$title .= "<a href='tiki-browse_categories.php?parentId=" . $id . "'>" . $cat['name'] . "</a> ";
		}

		// keep track of how many categories there are for split mode off
		$count++;

		// check if sub=>true and get sub category data
		if (!(isset($sub))) {
			$sub = true;
		} elseif ($sub == 'no' or $sub == 'n' or $sub == 'false') {
			$sub = false;
		} else {
			$sub = true;
		}

		$subcategs = array();

		if ($sub) {
			$subcategs = $categlib->get_category_descendants($id);
		}

		// array with objects in category
		$objectcat = array();

		if ($sub) {
			// get all items for category and sub category
			$objectcat = $categlib->list_category_objects_deep($id, $offset, $maxRecords, $sort, $find);
		} else {
			// get all items for category
			$objectcat = $categlib->list_category_objects($id, $offset, $maxRecords, $sort, $find);
		}

		foreach ($objectcat["data"] as $obj) {
			$type = $obj["type"];
			// check if current type is in allowed type list: * = everything allowed
			if (($types == '*') || array_search($typetokens[strtolower($type)], $typesallowed)) {
				// remove duplicates in non-split mode
				if ($split == 'n' or $split == 'no' or $split == 'false') {
					if (!(in_multi_array($obj['name'], $listcat))) // TODO: check for name+type
						{
						$listcat[$typetitles["$type"]][] = $obj;
					}
				} else {
					$listcat[$typetitles["$type"]][] = $obj;
				}
			}
		}

		// split mode: appending onto $out each time
		if ($split == 'y' or $split == 'yes' or $split == 'true') {
			$smarty->assign("title", $title);

			$smarty->assign("listcat", $listcat);
			$out .= $smarty->fetch("tiki-simple_plugin.tpl");
			// reset array for next loop
			$listcat = array();
			// reset title
			$title = '';
			$count = 0;
		}
	}

	// non-split mode
	if ($split == 'n' or $split == 'no' or $split == 'false') {
		$smarty->assign("title", $title);

		$smarty->assign("listcat", $listcat);
		$out = $smarty->fetch("tiki-simple_plugin.tpl");
	}

	return $out;
}

?>