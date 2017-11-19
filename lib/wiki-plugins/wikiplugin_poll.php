<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_poll_info()
{
	return [
		'name' => tra('Poll'),
		'documentation' => 'PluginPoll',
		'description' => tra('Embed a poll'),
		'prefs' => [ 'feature_polls', 'wikiplugin_poll' ],
		'body' => tra('Title of the poll'),
		'iconname' => 'thumbs-up',
		'introduced' => 1,
		'tags' => [ 'basic' ],
		'params' => [
			'pollId' => [
				'required' => true,
				'name' => tra('Poll'),
				'description' => tra('Numeric value representing the poll ID'),
				'since' => '1',
				'default' => '',
				'filter' => 'digits',
				'profile_reference' => 'poll',
			],
			'showtitle' => [
				'required' => false,
				'name' => tra('Show Title'),
				'description' => tra('Show poll title (shown by default).'),
				'since' => '5.0',
				'default' => 'y',
				'filter' => 'alpha',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				]
			],
			'showresult' => [
				'required' => false,
				'name' => tra('Show result'),
				'description' => tr('Set how results of the poll will be shown (default is %0link%1)', '<code>', '</code>'),
				'since' => '7.0',
				'filter' => 'word',
				'default' => 'link',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Link'), 'value' => 'link'],
					['text' => tra('Voted'), 'value' => 'voted'],
					['text' => tra('Always'), 'value' => 'always'],
				]
			],
			'showtotal' => [
				'required' => false,
				'name' => tra('Show total votes'),
				'description' => tr('Set to No (%0n%1) to not show votes. Default is Yes (%0y%1).', '<code>', '</code>'),
				'since' => '7.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n']
				]
			],
		],
	];
}

function wikiplugin_poll($data, $params)
{
	global $tiki_p_admin, $prefs, $user;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$polllib = TikiLib::lib('poll');
	$smarty = TikiLib::lib('smarty');
	$trklib = TikiLib::lib('trk');

	$default = ['showtitle' => 'y', 'showresult' => 'link', 'showtotal' => 'y'];
	$params = array_merge($default, $params);

	extract($params, EXTR_SKIP);

	if (! isset($pollId)) {
		return WikiParser_PluginOutput::argumentError(['pollId']);
	}
	$polllib = TikiLib::lib('poll');


	$poll_info = $polllib->get_poll($pollId);
	$options = $polllib->list_poll_options($pollId);

	$hasVoted = $tikilib->user_has_voted($user, 'poll' . $pollId);
	$userVote = $tikilib->get_user_vote('poll' . $pollId, $user);
	$ret = '';
	$smarty->assign_by_ref('showresult', $showresult);
	$smarty->assign_by_ref('showtotal', $showtotal);
	$smarty->assign_by_ref('hasVoted', $hasVoted);
	$smarty->assign_by_ref('showtitle', $showtitle);
	if ($poll_info['active'] != 'x' && (! $hasVoted || $prefs['feature_poll_revote'] == 'y')) {
		$smarty->assign_by_ref('menu_info', $poll_info);
		$smarty->assign_by_ref('channels', $options);
		$smarty->assign_by_ref('poll_title', $data);
		$smarty->assign('ownurl', $tikilib->httpPrefix() . $_SERVER['REQUEST_URI']);

		ask_ticket('poll-form');

		$ret = $smarty->fetch('tiki-plugin_poll.tpl');
	}
	if (($showresult == 'voted' && $hasVoted) || $showresult == 'always') {
		$total = $polllib->options_percent($poll_info, $options);
		$poll_info['options'] = $options;
		$smarty->assign_by_ref('poll_info', $poll_info);
		$smarty->assign_by_ref('user_vote', $userVote);
		$ret .= $smarty->fetch('tiki-poll_results_bar.tpl');
	}
	return '~np~' . $ret . '~/np~';
}
