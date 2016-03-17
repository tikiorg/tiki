<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * This is a like smarty function. It sets a thumb button link that allows you to like (and unlike) an object. It also
 * provides a count of likes thus far on that particular object. 
 * @param $params
 *  - type - sets the type of object being liked. Ex: trackeritem, wiki, article, etc
 *  - object - sets the id of the object being liked. 
 * 	- count_label - default "Likes" - sets the label after the count. Ex: "recommendations" would show "54 recommendations" instead of |Likes"
 *  - count_only - default 'false' - sets whether to only show the count. 
 * 
 * @param $smarty
 * @return string|void
 * @throws Exception
 */
function smarty_function_like($params, $smarty)
{
	global $prefs, $user, $smarty;

	// unregistered user, do nothing
	if (empty($user) || $prefs['user_likes'] != 'y') {
		return;
	}
	if (empty($params['count_label'])) {
		$count_label = "Likes";
	} else {
		$count_label = $params['count_label'];
	}
	if (empty($params['count_only'])) {
		$count_only = false;
	} else {
		$count_only = $params['count_only'];
	}

	$relation = "tiki.user.like";
	$relationlib = TikiLib::lib("relation");
	//if relation exists
	$relation_id = $relationlib->get_relation_id($relation, "user", $user, $params['type'], $params['object']);
	if ($relation_id) {
		$smarty->assign('has_relation', true);
	} else {
		$smarty->assign('has_relation', false);
	}
	$count = $relationlib->get_relation_count("tiki.user.like", $params['type'], $params['object']);

	$smarty->assign('type', $params['type']);
	$smarty->assign('object', $params['object']);
	$smarty->assign('count', $count);
	$smarty->assign('count_label', $count_label);
	$smarty->assign('count_only', $count_only);
	return $smarty->fetch('like.tpl');
}

