<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ( isset($_REQUEST['pollVote']) && !empty($_REQUEST['polls_pollId']) ) {
	$ok = true;
	if (empty($_REQUEST['polls_optionId'])) {
		$ok = false;
		$smarty->assign('msg_poll', tra('You must choose an option'));
	} elseif ( $tiki_p_vote_poll == 'y' && ($prefs['feature_poll_anonymous'] == 'y' || $user || $prefs['feature_antibot'] == 'y')) {
		if (empty($user) && empty($_COOKIE)) {
			$ok = false;
			$smarty->assign('msg_poll', tra('For you to vote, cookies must be allowed'));
			$smarty->assign_by_ref('polls_optionId', $_REQUEST['polls_optionId']);
		} elseif (($prefs['feature_antibot'] == 'y' && empty($user)) && (!isset($_SESSION['random_number']) || $_SESSION['random_number'] != $_REQUEST['antibotcode'])) {
			$ok = false;
			$smarty->assign('msg_poll', tra('You have mistyped the anti-bot verification code; please try again.'));
			$smarty->assign_by_ref('polls_optionId', $_REQUEST['polls_optionId']);
		} else {
			if( $tikilib->register_user_vote($user, 'poll' . $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId'], array(), $prefs['feature_poll_revote'] == 'y' ) ) {
				global $polllib; include_once('lib/polls/polllib_shared.php');
				$polllib->poll_vote($user, $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId']);
			}
		}
	}
	if ( $ok && ! isset($_REQUEST['wikipoll']) && $tiki_p_view_poll_results == 'y') {
		header ('location: tiki-poll_results.php?pollId='.$_REQUEST['polls_pollId']);
		die;
	}
}
