<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//copy this file to lib/smarty_tiki
//create a new module and put the following
//{wikistructure id=1 detail=1}
//id for structure id, or page_ref_id
//detail if you only wanna display subbranches of the open node within the structure
// assign your module


//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_wikistructure($params, $smarty)
{
	include_once('lib/wiki-plugins/wikiplugin_toc.php');

	if (!empty($params['id'])) {
		$params['structId'] = $params['id'];
	}
	$html = wikiplugin_toc('', $params);
	$html = str_replace(array('~np~', '~/np~'), '', $html);
	return $html;
}
