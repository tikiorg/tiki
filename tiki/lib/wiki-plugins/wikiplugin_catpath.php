<?php
/* $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_catpath.php,v 1.3 2003-08-07 03:02:11 rossta Exp $
 *
 * TikiWiki CATPATH plugin.
 * 
 * Syntax:
 * 
 * {CATPATH(
 *          divider=>string	#string that separates the categories, defaults to '>'
 *          top=>yes|no		#to display the TOP category or not, defaults to 'no'
 *         )}
 * {CATPATH}
 * 
  */
function wikiplugin_catpath_help() {
	return tra("Insert the full category path for each category that this wiki page belongs to");
}

function wikiplugin_catpath($data, $params) {
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

	// default divider is '>'
	if (!(isset($divider))) {
		$divider = '>';
	}

	// default setting for top is 'no'
	if (!(isset($top))) {
		$top = 'no';
	} elseif ($top != 'y' and $top != 'yes' and $top != 'n' and $top != 'no') {
		$top = 'no';
	}

	$objId = urldecode($_REQUEST['page']);

	$cats = $categlib->get_object_categories('wiki page', $objId);

	$catpath = '';

	foreach ($cats as $categId) {
		$catpath .= '<span class="categpath">';

		// Display TOP on each line if wanted
		if ($top == 'yes' or $top == 'y') {
			$catpath .= '<a class="categpath" href="tiki-browse_categories.php?parentId=0">TOP</a> ' . $divider . ' ';
		}

		$path = '';
		$info = $categlib->get_category($categId);
		$path
			= '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . $info["name"] . '</a>';

		while ($info["parentId"] != 0) {
			$info = $categlib->get_category($info["parentId"]);

			$path = '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . $info["name"] . '</a> ' . $divider . ' ' . $path;
		}

		$catpath .= $path . '</span><br/>';
	}

	return $catpath;
}

?>