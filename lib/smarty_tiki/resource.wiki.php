<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-view_tracker_item.php,v 1.141.2.24 2008-02-28 14:57:12 sylvieg Exp $
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.wiki.php
 * Type:     resource
 * Name:     wiki
 * Purpose:  Fetches a template from a wiki page
 * -------------------------------------------------------------
 */
function smarty_resource_wiki_source($page, &$tpl_source, &$smarty) {
	global $tikilib;
	$info = $tikilib->get_page_info($page);
	if (empty($info)) {
		return false;
	}
	$tpl_source = $tikilib->parse_data($info['data'],$info['is_html']);
	return true;
}

function smarty_resource_wiki_timestamp($page, &$tpl_timestamp, &$smarty) {
	global $tikilib;
	$info = $tikilib->get_page_info($page);
	if (empty($info)) {
		return false;
	}
	$tpl_timestamp = $info['lastModif'];
	return true;
}

function smarty_resource_wiki_secure($tpl_name, &$smarty)
{
    return true;
}

function smarty_resource_wiki_trusted($tpl_name, &$smarty)
{
    return true;
}
?> 