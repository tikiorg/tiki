<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ( isset($_REQUEST['pollVote']) ) {
	if ( $tiki_p_vote_poll == 'y' && isset($_REQUEST['polls_optionId']) ) {
		if( $prefs['feature_poll_anonymous'] == 'y' || $user ) {
			global $polllib; include_once('lib/polls/polllib_shared.php');
			if (($prefs['feature_poll_revote'] == 'y' && $user) || !$tikilib->user_has_voted($user, 'poll'.$_REQUEST['polls_pollId'])) {
				$polllib->poll_vote($user, $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId']);
				$tikilib->register_user_vote($user, 'poll' . $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId']);
			}
		}
	}
	$pollId = $_REQUEST['polls_pollId'];
	if ( ! isset($_REQUEST['wikipoll']) && $tiki_p_view_poll_results == 'y') {
		header ("location: tiki-poll_results.php?pollId=$pollId");
	}
}
