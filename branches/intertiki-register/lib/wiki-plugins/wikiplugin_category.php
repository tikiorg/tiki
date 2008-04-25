<?php

/*
 * $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_category.php,v 1.19 2007-10-12 07:55:48 nyloth Exp $
 *
 * Tiki-Wiki CATEGORY plugin.
 * 
 * Syntax:
 * 
 * {CATEGORY(
 *	id=>1+2+3,	 # defaults to current
 *	types=>article+blog+directory+faq+fgal+forum+igal+newsletter+event+poll+quiz+survey+tracker+wiki # list of types of objects, default * (all),
 *	sort=>[type|created|name|hits]_[asc|desc]	# default name_asc,
 *	sub=>y|n		# display items of subcategories # default is 'true';
 *	split=>y|n		# when displaying multiple categories, whether they should be split or not; defaults to yes
 *	one=>y|n		# when y displays one categoy per line
 * )}
 * {CATEGORY}
 * 
  */
function wikiplugin_category_help() {
	return tra("Insert list of items for the current/given category into wiki page").":<br />~np~{CATEGORY(id=>1+2+3, types=>article+blog+faq+fgal+forum+igal+newsletter+event+poll+quiz+survey+tracker+wiki+img, sort=>[type|created|name|hits|shuffle]_[asc|desc], sub=>y|n, split=>y|n)}{CATEGORY}~/np~";
}

function wikiplugin_category($data, $params) {
	global $smarty, $prefs, $categlib;

	if (!is_object($categlib)) {
		require_once ("lib/categories/categlib.php");
	}

	if ($prefs['feature_categories'] != 'y') {
		return "<span class='warn'>" . tra("Categories are disabled"). "</span>";
	}

	extract ($params,EXTR_SKIP);

	// TODO: use categ name instead of id (alternative)
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
	if (isset($and) and substr(strtolower($and),0,1) == 'y') {
		$and = true;
	} else {
		$and = false;
	}
	$sort = (isset($sort)) ? $sort : "";
	$types = (isset($types)) ? strtolower($types) : "*";
	
	$id = (isset($id)) ? $id : 'current'; // use current category if none is given
	if (isset($one) && $one == 'y')
		$smarty->assign('one', $one);

	if ($id == 'current') {
		$objId = urldecode($_REQUEST['page']);
		$catids = $categlib->get_object_categories('wiki page', $objId);
	} else {
		$catids = explode("+", $id);
	}

	return "~np~". $categlib->get_categoryobjects($catids,$types,$sort,$split,$sub,$and)."~/np~";
}

?>
