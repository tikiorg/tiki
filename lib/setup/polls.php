<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

if ( isset($_REQUEST['pollVote']) ) {
	if ( $tiki_p_vote_poll == 'y' && isset($_REQUEST['polls_optionId']) ) {
		if( $prefs['feature_poll_anonymous'] == 'y' || $user ) {
			if ( ! isset($polllib) or ! is_object($polllib) ) {
				include_once('lib/polls/polllib_shared.php');
			}
			$polllib->poll_vote($user, $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId']);
			// Poll vote must go first, or the new vote will be seen as the previous one.
			$tikilib->register_user_vote($user, 'poll' . $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId']);
		}
	}
	$pollId = $_REQUEST['polls_pollId'];
	if ( ! isset($_REQUEST['wikipoll']) ) {
		header ("location: tiki-poll_results.php?pollId=$pollId");
	}
}
