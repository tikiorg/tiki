<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

if ( isset($_REQUEST['pollVote']) && !empty($_REQUEST['polls_pollId']) ) {
	$ok = true;
	$voted = false;
	if (empty($_REQUEST['polls_optionId'])) {
		$ok = false;
		$smarty->assign('msg_poll', tra('You must choose an option'));
	} elseif ( $tiki_p_vote_poll == 'y' && ($prefs['feature_poll_anonymous'] == 'y' || $user || $prefs['feature_antibot'] == 'y')) {
		$captchalib = TikiLib::lib('captcha');
		if (empty($user) && empty($_COOKIE)) {
			$ok = false;
			$smarty->assign('msg_poll', tra('For you to vote, cookies must be allowed'));
			$smarty->assign_by_ref('polls_optionId', $_REQUEST['polls_optionId']);
		} elseif (($prefs['feature_antibot'] == 'y' && empty($user)) && (!$captchalib->validate())) {
			$ok = false;
			$smarty->assign('msg_poll', $captchalib->getErrors());
			$smarty->assign_by_ref('polls_optionId', $_REQUEST['polls_optionId']);
		} else {
			$polllib = TikiLib::lib('poll');
			$poll = $polllib->get_poll($_REQUEST['polls_pollId']);
			if ( empty($poll) || $poll['active'] == 'x' ) {
				$ok = false;
				$smarty->assign('msg_poll', tra('This poll is closed.'));
				$smarty->assign_by_ref('polls_optionId', $_REQUEST['polls_optionId']);
			} else {
				$previous_vote = $polllib->get_user_vote('poll' . $_REQUEST['polls_pollId'], $user);
				if ( $tikilib->register_user_vote($user, 'poll' . $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId'], array(), $prefs['feature_poll_revote'] == 'y')) {
					$polllib->poll_vote($user, $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId'], $previous_vote);
				}
			}
		}
	}
	if ( $ok && ! isset($_REQUEST['wikipoll']) && $tiki_p_view_poll_results == 'y' && empty($_REQUEST['showresult'])) {
		header('location: tiki-poll_results.php?pollId='.$_REQUEST['polls_pollId']);
		die;
	}
}
