<?php

/**
 * $Header: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_showpages.php,v 1.7 2007-10-12 07:55:48 nyloth Exp $
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
 * @package Tikiwiki
 * @subpackage TikiPlugins
 * @version $Revision: 1.7 $
 */

function wikiplugin_showpages_help() {
	return tra("List wiki pages").":<br />~np~{SHOWPAGES(find=>criteria [, max=>qty] [, display=>name|desc])/}~/np~";
}

function wikiplugin_showpages($data, $params) {
	global $tikilib, $prefs;

	extract ($params,EXTR_SKIP);
	if (!isset($find)) {
		return ("<b>missing find parameter for plugin SHOWPAGES</b><br />");
	}

	if (!isset($max)) {
		$max = -1;
	}

	if (!isset($display) || (strpos($display,'name') === false && strpos($display,'desc') === false)) {
		$display = 'name|desc';
	}

	$data = $tikilib->list_pages(0, $max, 'pageName_asc', $find);

	$text = '';

	foreach ($data["data"] as $page) {
		if (isset($prefs['feature_wiki_description']) && $prefs['feature_wiki_description'] == 'y' && strpos($display,'desc') !== false) {
			$desc = $tikilib->page_exists_desc($page["pageName"]);
		} else {
			$desc = '';
		}
		$text .= "<a href=\"tiki-index.php?page=".$page["pageName"]."\" title=\"".tra("Last modified by")." ".$page["user"]."\" class=\"wiki\">";
		$text .= (strpos($display,'name') !== false || strlen($desc) == 0 ? $page["pageName"] : $desc);
		$text .= "</a>";
		$text .= (strpos($display,'name') !== false && $desc !== $page["pageName"] && strlen($desc) > 0 ? " - $desc" : "");
		$text .= "<br />";
	}

	return $text;
}

?>
