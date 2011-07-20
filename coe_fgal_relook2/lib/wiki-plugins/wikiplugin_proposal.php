<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_proposal_info() {
	return array(
		'name' => tra('Proposal'),
		'documentation' => 'PluginProposal',
		'description' => tra('Allow users to vote on a proposal and view the results'),
		'prefs' => array( 'wikiplugin_proposal' ),
		'body' => tra('The list of votes cast. One vote per line. Either 0, +1 or -1 followed by a username.'),
		'icon' => 'pics/icons/thumb_up.png',
		'params' => array(
			'caption' => array(
				'required' => false,
				'name' => tra( 'Caption' ),
				'description' => tra('Short description of the proposal to vote on. Will be displayed above the result table.'),
				'default' => '',
			)
		)
	);
}

function wikiplugin_proposal_save( $context, $data, $params ) {
	global $attributelib; require_once 'lib/attributes/attributelib.php';

	static $objects = array();
	$key = $context['type'] . ':' . $context['object'];

	if( ! isset( $objects[$key] ) ) {
		$objects[$key] = array( '+1' => 0, '0' => 0, '-1' => 0 );
	}

	$counts = wikiplugin_proposal_get_counts( $data );

	$objects[$key]['+1'] += count( $counts['+1'] );
	$objects[$key]['0'] += count( $counts['0'] );
	$objects[$key]['-1'] += count( $counts['-1'] );
	
	$attributelib->set_attribute( $context['type'], $context['object'], 'tiki.proposal.accept', $objects[$key]['+1'] );
	$attributelib->set_attribute( $context['type'], $context['object'], 'tiki.proposal.undecided', $objects[$key]['0'] );
	$attributelib->set_attribute( $context['type'], $context['object'], 'tiki.proposal.reject', $objects[$key]['-1'] );
}

function wikiplugin_proposal($data, $params) {
	$counts = wikiplugin_proposal_get_counts( $data );

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

function wikiplugin_proposal_get_counts( $data ) {
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
	
	return $counts;
}

