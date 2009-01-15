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
			if ( ! isset($polllib) or ! is_object($polllib) ) {
				include_once('lib/polls/polllib_shared.php');
			}
			$identification = $user;
			$pollinfo = $polllib->get_poll($_REQUEST['polls_pollId']);
			$anonym=$pollinfo['anonym'];
			if($anonym=='i') $identification=$tikilib->get_ip_address();
			if($anonym=='c') $identification = ( isset($_COOKIE['tiki_wiki_poll_'.$_REQUEST['polls_pollId']])
				? $_COOKIE['tiki_wiki_poll_'.$_REQUEST['polls_pollId']] : MD5(time().'_'.rand(0,1000)) );
			if($anonym=='a'||!$polllib->id_has_voted($_REQUEST['polls_pollId'],$identification)) {
				$polllib->poll_vote($user, $_REQUEST['polls_pollId'], $_REQUEST['polls_optionId']);
				// Poll vote must go first, or the new vote will be seen as the previous one.
				if($anonym!='a') $polllib->register_id_vote($_REQUEST['polls_pollId'], $_REQUEST['polls_optionId'],$identification);
				if($anonym=='c') setcookie('tiki_wiki_poll_'.$_REQUEST['polls_pollId'],$identification,time()+60*60*24*300);
			}
		}
	}
	$pollId = $_REQUEST['polls_pollId'];
	if ( ! isset($_REQUEST['wikipoll']) ) {
		header ("location: tiki-poll_results.php?pollId=$pollId");
	}
}
