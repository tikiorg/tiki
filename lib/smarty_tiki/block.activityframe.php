<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_activityframe($params, $content, $smarty, &$repeat)
{
	if ( $repeat ) return;

	$likes = isset($params['activity']['like_list']) ? $params['activity']['like_list'] : array();
	if (! is_array($likes)) {
		$params['activity']['like_list'] = $likes = array();
	}
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('activityframe', array(
		'content' => $content,
		'activity' => $params['activity'],
		'heading' => $params['heading'],
		'like' => in_array($GLOBALS['user'], $likes),
	));
	$out = $smarty->fetch('activity/activityframe.tpl');

	return $out;
}
