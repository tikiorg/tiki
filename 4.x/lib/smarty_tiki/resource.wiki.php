<?php
// $Id: $
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
/**
 * \brief Smarty plugin to use wiki page as a template resource
 * -------------------------------------------------------------
 * File:     resource.wiki.php
 * Type:     resource
 * Name:     wiki
 * Purpose:  Fetches a template from a wiki page
 * -------------------------------------------------------------
 */
function smarty_resource_wiki_source($page, &$tpl_source, &$smarty) {
	global $tikilib, $user;

	 if (!$tikilib->user_has_perm_on_object($user, $page, 'wiki page', 'tiki_p_use_as_template')) {
		$tpl_source= tra('Permission denied: the specified wiki page cannot be used as Smarty template resource').'<br />';
		// TODO: do not cache ! and return the message only once should be enough...
		return true;
	 }

	$info = $tikilib->get_page_info($page);
	if (empty($info)) {
		return false;
	}
	$tpl_source = $tikilib->parse_data($info['data'], array('is_html' => $info['is_html'], 'print'=>'y'));
	return true;
}

function smarty_resource_wiki_timestamp($page, &$tpl_timestamp, &$smarty) {
	global $tikilib, $user;
	$info = $tikilib->get_page_info($page);
	if (empty($info)) {
		return false;
	}
	if (preg_match("/\{([A-Z0-9_]+) */", $info['data'])) { // there are some plugins - so it can be risky to cache the page
		$tpl_timestamp = $tikilib->now;
	} else {
		$tpl_timestamp = $info['lastModif'];
	}
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
