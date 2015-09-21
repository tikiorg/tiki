<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_proposal_info()
{
	return array(
		'name' => tra('Proposal'),
		'documentation' => 'PluginProposal',
		'description' => tra('Allow users to vote on a proposal and view the results'),
		'prefs' => array( 'wikiplugin_proposal' ),
		'body' => tra('The list of votes cast. One vote per line. Either 0, +1 or -1 followed by a username.'),
		'iconname' => 'thumbs-up',
		'introduced' => 3,
		'tags' => array( 'basic' ),
		'params' => array(
			'caption' => array(
				'required' => false,
				'name' => tra('Caption'),
				'description' => tra('Short description of the proposal to vote on. Will be displayed above the result
					table.'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => '',
			),
			'weights' => array(
				'required' => false,
				'advanced' => true,
				'name' => tr('Weights'),
				'description' => tr('Comma-separated list of groups and their associated weight. Default is
					%0Registered(1)%1 Example:', '<code>', '</code>')
					. ' <code>Reviewer(2.5),User(1),Manager(0.25),Registered(0)</code>',
				'since' => '10.0',
				'filter' => 'text',
				'default' => 'Registered(1)',
			),
		)
	);
}

function wikiplugin_proposal_save( $context, $data, $params )
{
	$attributelib = TikiLib::lib('attribute');

	static $objects = array();
	$key = $context['type'] . ':' . $context['object'];

	if ( ! isset( $objects[$key] ) ) {
		$objects[$key] = array( '+1' => 0, '0' => 0, '-1' => 0 );
	}

	$counts = wikiplugin_proposal_get_counts($data);

	$objects[$key]['+1'] += $counts['weights']['+1'];
	$objects[$key]['0'] += $counts['weights']['0'];
	$objects[$key]['-1'] += $counts['weights']['-1'];
	
	$attributelib->set_attribute($context['type'], $context['object'], 'tiki.proposal.accept', $objects[$key]['+1']);
	$attributelib->set_attribute($context['type'], $context['object'], 'tiki.proposal.undecided', $objects[$key]['0']);
	$attributelib->set_attribute($context['type'], $context['object'], 'tiki.proposal.reject', $objects[$key]['-1']);
}

function wikiplugin_proposal($data, $params)
{
	$counts = wikiplugin_proposal_get_counts($data);
	unset($counts['weights']);
	global $user, $tiki_p_edit;
	$smarty = TikiLib::lib('smarty');
	$smarty->assign('counts', $counts);

	if ( $user && $tiki_p_edit == 'y' ) {
		$weight = wikiplugin_proposal_get_weight($user, $params);
		$availableVotes = array(
			tra('Accept proposal') => "$data\n+1~$weight $user",
			tra('Still undecided') => "$data\n0~$weight $user",
			tra('Reject proposal') => "$data\n-1~$weight $user",
		);

		$smarty->assign('available_votes', $availableVotes);
	}

	static $passes;
	$smarty->assign('passes', ++$passes);
	$smarty->assign('params', $params);
	$content = $smarty->fetch('wiki-plugins/wikiplugin_proposal.tpl');

	return "~np~$content~/np~";
}

function wikiplugin_proposal_get_counts( $data ) 
{
	$voteData = explode("\n", $data);
	$votes = array();

	foreach ( $voteData as $entry ) {
		$entry = trim($entry);
		if ( preg_match("/^(([\+\-]1)|0)(~(\d+(\.\d+)?))?\s+(\w+)/", $entry, $parts) ) {
			list( $full, $vote, $null, $null, $weight, $null, $voter ) = $parts;
			if (strlen($weight) == 0) {
				$weight = 1;
			} else {
				$weight = (float) $weight;
			}

			$votes[$voter] = array($vote, $weight);
		}
	}

	$counts = array(
		'+1' => array(),
		 '0' => array(),
		'-1' => array(),
		'weights' => array(
			'+1' => 0,
			 '0' => 0,
			'-1' => 0,
		),
	);

	foreach ( $votes as $voter => $values ) {
		list($vote, $weight) = $values;
		$counts[$vote][] = $voter;
		$counts['weights'][$vote] += $weight;
	}
	
	return $counts;
}

function wikiplugin_proposal_get_weight($user, array $params)
{
	$weights = array();
	
	if (isset($params['weights'])) {
		$parts = explode(',', $params['weights']);
		foreach ($parts as $part) {
			if (preg_match('/^(.+)\((\d+(\.\d+)?)\)$/', $part, $segments)) {
				$weights[trim($segments[1])] = (float) $segments[2];
			}
		}
	}

	if (count($weights) == 0) {
		$weights['Registered'] = 1.0;
	}

	$groups = Perms::get()->getGroups();

	foreach ($weights as $group => $weight) {
		if (in_array($group, $groups)) {
			return $weight;
		}
	}

	return 0;
}

