<?php

function smarty_function_rating( $params, $smarty ) {
	global $ratinglib; require_once 'lib/rating/ratinglib.php';

	if( ! isset( $params['type'], $params['id'] ) ) {
		return tra('No object information provided for rating.');
	}

	$type = $params['type'];
	$id = $params['id'];

	if( isset( $_REQUEST['rating_value'][$type][$id] ) ) {
		$value = $_REQUEST['rating_value'][$type][$id];
		if( $ratinglib->record_vote( $type, $id, $value ) ) {

			// Handle type-specific actions
			if( $type == 'comment' ) {
				global $commentslib, $user; require_once 'lib/commentslib.php';

				if( $user ) {
					$commentslib->vote_comment( $id, $user, $value );
				}
			}

			return tra('Your vote was recorded.');
		} else {
			return tra('Your vote could not be recorded. You may have voted before.');
		}
	}

	$vote = $ratinglib->get_vote( $type, $id );

	$smarty->assign( 'rating_type', $type );
	$smarty->assign( 'rating_id', $id );
	$smarty->assign( 'rating_options', $ratinglib->get_options( $type ) );
	$smarty->assign( 'current_rating', $vote );
	return $smarty->fetch( 'rating.tpl' );
}

