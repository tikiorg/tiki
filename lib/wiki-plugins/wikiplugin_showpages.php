<?php

/**
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_showpages.php,v 1.3 2005-01-22 22:55:56 mose Exp $
 *
 * SHOWPAGES plugin
 * Displays wiki pages that match a supplied pagename criteria.
 *
 * Usage:
 * {SHOWPAGES(find=>namepattern [, max=>qty])/}
 *
 * Params:
 * @param	namepattern	Search criteria for the page
 * @param	qty		Max qty of results to return
 *
 * Notes:
 * Make sure that the wiki page this is used on is not cached if you want more "live" results.
 * under normal loads and sites, this should be cached.
 *
 * @package TikiWiki
 * @subpackage TikiPlugins
 * @version $Revision: 1.3 $
 */

function wikiplugin_showpages_help() {
	return tra("List wiki pages").":<br />~np~{SHOWPAGES(find=>criteria [, max=>qty])/}~/np~";
}

function wikiplugin_showpages($data, $params) {
	global $tikilib, $feature_wiki_description;

	extract ($params,EXTR_SKIP);
	if (!isset($find)) {
		return ("<b>missing find parameter for plugin SHOWPAGES</b><br />");
	}

	if (!isset($max)) {
		$max = -1;
	}

	$data = $tikilib->list_pages(0, $max, 'pageName_asc', $find);

	$text = '';

	foreach ($data["data"] as $page) {
		$text .= "<a href=\"tiki-index.php?page=".$page["pageName"]."\" title=\"".tra("Last modified by")." ".$page["user"]."\" class=\"wiki\">".$page["pageName"]."</a>";
		if (isset($feature_wiki_description) && $feature_wiki_description == 'y') {
			$text .= " - ".$tikilib->page_exists_desc($page["pageName"]);
		}
		$text .= "<br />";

	}
	
	return $text;
}

?>
