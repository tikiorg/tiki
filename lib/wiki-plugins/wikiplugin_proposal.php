<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// Wiki plugin to display a proposal acceptance widget

function wikiplugin_proposal_help() {
        return tra("Displays a proposal acceptance widget").":<br />~np~{AGENTINFO(info=>IP or SVRSW or BROWSER)/}~/np~";
}

function wikiplugin_proposal_info() {
	return array(
		'name' => tra('Proposal'),
		'documentation' => 'PluginProposal',			
		'description' => tra('Provides a widget for users to vote on a proposal and view the current decision.'),
		'prefs' => array( 'wikiplugin_proposal' ),
		'body' => tra('The list of votes cast. One vote per line. Either 0, +1 or -1 followed by a username.'),
		'params' => array(
			'caption' => array(
				'required' => false,
				'name' => tra( 'Caption' ),
				'description' => tra("Short description of the proposal to vote on. Will be displayed above the result table."),
			),
		),
	);
}

function wikiplugin_proposal($data, $params) {
	$voteData = explode( "\n", $data );
	$votes = array();

	foreach( $voteData as $entry )
	{
		$entry = trim( $entry );
		if( preg_match( "/^(([\+\-]1)|0)\s+(\w+)/", $entry, $parts ) )
		{
			list( $full, $vote, $null, $voter ) = $parts;

			$votes[$voter] = $vote;
		}
	}

	$counts = array(
		'+1' => array(),
		 '0' => array(),
		'-1' => array(),
	);

	foreach( $votes as $voter => $vote )
		$counts[$vote][] = $voter;
	
	global $smarty, $user, $tiki_p_edit;
	$smarty->assign( 'counts', $counts );

	if( $user && $tiki_p_edit == 'y' )
	{
		$availableVotes = array(
			tra('Accept proposal') => "$data\n+1 $user",
			tra('Still undecided') => "$data\n0 $user",
			tra('Reject proposal') => "$data\n-1 $user",
		);

		$smarty->assign( 'available_votes', $availableVotes );
	}

	static $passes;
	$smarty->assign( 'passes', ++$passes );
	$smarty->assign( 'params', $params );
	$content = $smarty->fetch( 'wiki-plugins/wikiplugin_proposal.tpl' );

	return "~np~$content~/np~";
}
