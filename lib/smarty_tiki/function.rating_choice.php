<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_rating_choice( $params, $smarty )
{
	global $prefs, $user;
	$ratinglib = TikiLib::lib('rating');

	if ( ! isset($params['comment_author'], $params['type'], $params['id']) ) {
		return tra('No object information provided for rating.');
	}

	$comment_author = $params['comment_author'];
	$type = $params['type'];
	$id = $params['id'];

	$vote = $ratinglib->get_vote_comment_author($comment_author, $type, $id);
	$options = $ratinglib->get_options($type, $id);

	if ($prefs['rating_smileys'] == 'y') {
		$smiles = $ratinglib->get_options_smiles($type, $id);
		$smarty->assign('rating_smiles', $smiles);
	}

	$smarty->assign('rating_type', $type);
	$smarty->assign('rating_id', $id);
	$smarty->assign('rating_options', $options);
	$smarty->assign('current_rating', $vote);
	return $smarty->fetch('rating_choice.tpl');
}

