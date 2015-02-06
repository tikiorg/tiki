<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
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
function smarty_resource_wiki_source($page, &$tpl_source, $smarty)
{
	global $tikilib, $user;

	$perms = Perms::get(array( 'type' => 'wiki page', 'object' => $page ));
	if ( ! $perms->use_as_template ) {
		$tpl_source= tra('Permission denied: the specified wiki page cannot be used as Smarty template resource').'<br />';
		// TODO: do not cache ! and return the message only once should be enough...
		return true;
	}

	$info = $tikilib->get_page_info($page);
	if (empty($info)) {
		return false;
	}
	$tpl_source = $tikilib->parse_data($info['data'], array('is_html' => $info['is_html'], 'print'=>'y', 'inside_pretty' => true));
	return true;
}

function smarty_resource_wiki_timestamp($page, &$tpl_timestamp, $smarty)
{
	global $tikilib, $user;
	$info = $tikilib->get_page_info($page);
	if (empty($info)) {
		return false;
	}
	if (preg_match('/\{([A-z-Z0-9_]+) */', $info['data']) || preg_match('/\{\{.+\}\}/', $info['data'])) { // there are some plugins - so it can be risky to cache the page
		$tpl_timestamp = $tikilib->now + 100; // future needed in case consecutive run of template;
	} else {
		$tpl_timestamp = $info['lastModif'];
	}
	return true;
}

function smarty_resource_wiki_secure($tpl_name, $smarty)
{
    return true;
}

function smarty_resource_wiki_trusted($tpl_name, $smarty)
{
    return true;
}
