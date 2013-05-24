<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function smarty_function_rating( $params, $smarty )
{
	global $prefs, $ratinglib;
	require_once 'lib/rating/ratinglib.php';

	if ( ! isset($params['type'], $params['id']) ) {
		return tra('No object information provided for rating.');
	}

	$type = $params['type'];
	$id = $params['id'];
	if ( isset($params['changemandated']) && $params['changemandated'] == 'y' ) {
		$changemandated = true; // needed to fix multiple submission problem in comments
	} else {
		$changemandated = false;
	}

	if ( isset( $_REQUEST['rating_value'][$type][$id], $_REQUEST['rating_prev'][$type][$id] ) ) {
		$value = $_REQUEST['rating_value'][$type][$id];
		$prev = $_REQUEST['rating_prev'][$type][$id];
		if ( ( !$changemandated || $value != $prev ) && $ratinglib->record_vote($type, $id, $value) ) {

			// Handle type-specific actions
			if ( $type == 'comment' ) {
				global $user; require_once 'lib/comments/commentslib.php';

				if ( $user ) {
					$commentslib = new Comments();
					$commentslib->vote_comment($id, $user, $value);
				}
			} elseif($type == 'article' ) {
				global $artlib, $user; require_once 'lib/articles/artlib.php';
				if ( $user ) {
				  $artlib->vote_comment($id, $user, $value);
				}
	        }
			
			if ($prefs['feature_score'] == 'y' && $id) {
				global $tikilib;
				if ($type == 'comment') {
				  $forum_id = $commentslib->get_comment_forum_id($id);
				  $forum_info = $commentslib->get_forum($forum_id);
				  $thread_info = $commentslib->get_comment($id, null, $forum_info);
				  $item_user = $thread_info['userName'];
				} elseif($type == 'article') {
				  require_once 'lib/articles/artlib.php';
				  $artlib = new ArtLib();
				  $res = $artlib->get_article($id);
				  $item_user = $res['author'];
				}
				if ($value == '1') {
				  $tikilib->score_event($item_user, 'item_is_rated', "$user:$type:$id");
				} elseif($value == '2') {
				  $tikilib->score_event($item_user, 'item_is_unrated', "$user:$type:$id");
				}
			}
		} elseif ( $value != $prev ) {
			return tra('An error occurred.');
		}
	}

	$vote = $ratinglib->get_vote($type, $id);
	$options = $ratinglib->get_options($type, $id);

	if ($prefs['rating_smileys'] == 'y') {
		$smiles = $ratinglib->get_options_smiles($type, $id);
		$smarty->assign('rating_smiles', $smiles);
	}

	$smarty->assign('rating_type', $type);
	$smarty->assign('rating_id', $id);
	$smarty->assign('rating_options', $options);
	$smarty->assign('current_rating', $vote);
	return $smarty->fetch('rating.tpl');
}

