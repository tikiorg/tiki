<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_ifsearchexists($params, $content, $smarty, &$repeat)
{
	if (empty($params['type']) || empty($params['id'])) {
		return '';
	}

	TikiLib::lib('access')->check_feature('feature_search');

	$query = new Search_Query;
	$query->addObject($params['type'], $params['id']);
	$index = TikiLib::lib('unifiedsearch')->getIndex();
	$result = $query->search($index);
	
	if ($result->count() > 0 ) {
		return $content;
	} else {
		return '';
	}
}
