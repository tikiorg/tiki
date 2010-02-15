<?php

function smarty_function_rating( $params, $smarty ) {
	global $tikilib, $user;

	if (!$user && !isset($_COOKIE['PHPSESSID'])) {
		return tra('Cookies must be enabled to vote.');
	}

	if( isset( $params['id'] ) ) {
		$key = $params['id'];
	} elseif( $params['comment'] ) {
		$key = 'comment' . $params['comment'];
	} else {
		return '<a href="">'.tra('No key provided for rating.').'</a>';
	}

	if( is_array( $params['options'] ) ) {
		$options = $params['options'];
	} else {
		$options = explode( ',', $params['options'] );
	}

	$revote = isset( $params['revote'] ) && $params['revote'] == 'y';

	if( isset( $_REQUEST['rating_key'], $_REQUEST['rating_value'] ) ) {
		if( $_REQUEST['rating_key'] == $key ) {
			if( $tikilib->register_user_vote( $user, $key, $_REQUEST['rating_value'], $options, $revote ) ) {

				// Handle type-specific actions
				if( isset( $params['comment'] ) ) {
					global $commentslib; require_once 'lib/commentslib.php';

					$commentslib->vote_comment( $params['comment'], $user, $_REQUEST['rating_value'] );
				}

				return '<a href="">'.tra('Your vote was recorded.').'</a>';
			} else {
				return '<a href="">'.tra('Your vote could not be recorded. You may have voted before.').'</a>';
			}
		}
	}

	$smarty->assign( 'rating_key', $key );
	$smarty->assign( 'rating_options', $options );
	$smarty->assign( 'rating_canvote', ! $tikilib->user_has_voted( $user, $key ) || $revote );
	return $smarty->fetch( 'rating.tpl' );
}

