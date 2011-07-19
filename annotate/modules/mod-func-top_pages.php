<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function module_top_pages_info() {
	return array(
		'name' => tra('Top Pages'),
		'description' => tra('Displays the specified number of wiki pages with links to them, starting with the one having the most hits.'),
		'prefs' => array( 'feature_wiki' ),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

function module_top_pages( $mod_reference, $module_params ) {
	global $smarty;
	global $ranklib; include_once ('lib/rankings/ranklib.php');
	$categs = $ranklib->get_jail();
	$ranking = $ranklib->wiki_ranking_top_pages($mod_reference["rows"], $categs ? $categs : array());
	
	$smarty->assign('modTopPages', $ranking["data"]);
}
