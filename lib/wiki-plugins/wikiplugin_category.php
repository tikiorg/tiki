<?php

/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_category.php,v 1.10 2004-10-08 10:00:01 damosoft Exp $
 *
 * Tiki-Wiki CATEGORY plugin.
 * 
 * Syntax:
 * 
 * {CATEGORY(
 *	id=>1+2+3,	 # defaults to current
 *	types=>article+blog+directory+faq+fgal+forum+igal+newsletter+poll+quiz+survey+tracker+wiki # list of types of objects, default * (all),
 *	sort=>[type|created|name|hits]_[asc|desc]	# default name_asc,
 *	sub=>true|false		# display items of subcategories # default is 'true';
 *	split=>y|n		# when displaying multiple categories, whether they should be split or not; defaults to yes
 *	title=>y|n|title		# is the category name displayed ? if 'n', it is not, if 'y' (default), it is displayed, if a text is given, it will replace the name of the category
 * )}
 * {CATEGORY}
 * 
  */
function wikiplugin_category_help() {
	return tra("Insert list of items for the current/given category into wiki page").":<br />~np~{CATEGORY(id=>1+2+3,types=>article+blog+faq+fgal+igal+newsletter+poll+quiz+survey+tracker+wiki+img,sort=>[type|created|name|hits]_[asc|desc],sub=>true|false,split=>y|n)}{CATEGORY}~/np~";
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

	// TODO: use categ name instead of id (alternative)
	$id = (isset($id)) ? $id : 'current'; // use current category if none is given

	if ($id == 'current') {
		$objId = urldecode($_REQUEST['page']);
		$catids = $categlib->get_object_categories('wiki page', $objId);
	} else {
		$catids = explode("+", $id);
	}
	if (isset($split) and substr(strtolower($split),0,1) == 'n') {
		$split = false;
	} else {
		$split = true;
	}
	if (isset($sub) and substr(strtolower($sub),0,1) == 'n') {
		$sub = false;
	} else {
		$sub = true;
	}
	$sort = (isset($sort)) ? $sort : "created_desc";
	$types = (isset($types)) ? strtolower($types) : "*";

	return $categlib->get_categoryobjects($catids,$types,$sort,$split,$sub);
}

?>
