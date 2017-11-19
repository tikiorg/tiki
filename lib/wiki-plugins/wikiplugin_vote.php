<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_vote_info()
{
	return [
		'name' => tra('Vote'),
		'documentation' => 'PluginVote',
		'description' => tra('Create a tracker for voting'),
		'prefs' => [ 'feature_trackers', 'wikiplugin_vote' ],
		'body' => tra('Title'),
		'iconname' => 'thumbs-up',
		'introduced' => 2,
		'params' => [
			'trackerId' => [
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('Numeric value representing the tracker ID'),
				'since' => '2.0',
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker',
			],
			'fields' => [
				'required' => true,
				'name' => tra('Fields'),
				'description' => tra('Colon-separated list of field IDs to be displayed. If not set all the fields that
					can be used (except IP, user, system, private fields) are used. Example:') . ' <code>2:4:5</code>',
				'since' => '2.0',
				'default' => '',
				'separator' => ':',
				'profile_reference' => 'tracker_field',
				'parent' => 'input[name="params[trackerId]"]',
				'parentkey' => 'tracker_id',
			],
			'show_percent' => [
				'required' => false,
				'name' => tra('Show Percentage'),
				'description' => tra('Choose whether to show the percentage of the vote each option received (not
					shown by default)'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'show_bar' => [
				'required' => false,
				'name' => tra('Show Bar'),
				'description' => tra('Choose whether to show a bar representing the number of votes each option
					received (not shown by default)'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => 'n',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'show_stat' => [
				'required' => false,
				'name' => tra('Show Stats'),
				'description' => tra('Choose whether to show the voting results (shown by default)'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => 'y',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'show_stat_only_after' => [
				'required' => false,
				'name' => tra('Show Stats After'),
				'description' => tra('Choose whether to show the voting results only after the date given in the
					tracker configuration (not set by default)'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'show_creator' => [
				'required' => false,
				'name' => tra('Show Creator'),
				'description' => tra('Choose whether to display the user name of the creator of the voting tracker (not
					shown by default)'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
			'status' => [
				'required' => false,
				'name' => tra('Status Filter'),
				'description' => tra('Only show items matching certain status filters'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => 'o',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Open'), 'value' => 'o'],
					['text' => tra('Pending'), 'value' => 'p'],
					['text' => tra('Closed'), 'value' => 'c'],
					['text' => tra('Open & Pending'), 'value' => 'op'],
					['text' => tra('Open & Closed'), 'value' => 'oc'],
					['text' => tra('Pending & Closed'), 'value' => 'pc'],
					['text' => tra('Open, Pending & Closed'), 'value' => 'opc'],
				],
			],
			'float' => [
				'required' => false,
				'name' => tra('Float'),
				'description' => tra('Align the plugin on the page, allowing other elements to wrap around it (not set
					by default)'),
				'since' => '2.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Left'), 'value' => 'left'],
					['text' => tra('Right'), 'value' => 'right'],
					['text' => tra('None'), 'value' => 'none'],
				],
			],
			'show_toggle' => [
				'required' => false,
				'name' => tra('Show Toggle'),
				'description' => tra('Show toggle or not to display the form and the results'),
				'since' => '10.0',
				'filter' => 'alpha',
				'default' => '',
				'options' => [
					['text' => '', 'value' => ''],
					['text' => tra('Yes'), 'value' => 'y'],
					['text' => tra('No'), 'value' => 'n'],
				],
			],
		],
	];
}

function wikiplugin_vote($data, $params)
{
	global $user, $prefs, $tiki_p_admin_trackers, $tiki_p_view_trackers;
	$trklib = TikiLib::lib('trk');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
	extract($params, EXTR_SKIP);

	if ($prefs['feature_trackers'] != 'y' || ! isset($trackerId) || ! ($tracker = $trklib->get_tracker($trackerId))) {
		return $smarty->fetch("wiki-plugins/error_tracker.tpl");
	}

	$smarty->assign_by_ref('tracker', $tracker);

	if (isset($float)) {
		$smarty->assign('float', $float);
	} else {
		$smarty->assign('float', '');
	}
	if ($trklib->get_user_item($trackerId, ['oneUserItem' => 'y'])) {
		$smarty->assign('has_already_voted', 'y');
	} else {
		$smarty->assign('has_already_voted', 'n');
	}
	if (empty($fields)) {
		$fields = $trklib->list_tracker_fields($trackerId);
		$ff = [];
		foreach ($fields['data'] as $field) {
			if ($field['type'] != 'u' && $field['type'] != 'I' && $field['type'] != 'g' && $field['isPublic'] == 'y') {
				$ff[] = $field['fieldId'];
			}
		}
		if (! empty($ff)) {
			$params['fields'] = $ff;
		}
	}
	if (isset($show_creator) && $show_creator == 'y') {
		$tracker = $trklib->get_tracker($trackerId);
		$smarty->assign_by_ref('tracker_creator', $tracker['user']);
	}
	$smarty->assign('options', '');
	if ($tikilib->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_create_tracker_items')) {
		$options = $trklib->get_tracker_options($trackerId);
		if (! empty($options['start']) || ! empty($options['end'])) {
			$smarty->assign_by_ref('options', $options);
		}
		if ((! empty($options['start']) && $tikilib->now < $options['start']) || (! empty($options['end']) && $tikilib->now > $options['end'])) {
			$smarty->assign('p_create_tracker_items', 'n');
			$smarty->assign('vote', '');
		} else {
			$smarty->assign('p_create_tracker_items', 'y');// to have different vote in the same page
			include_once('lib/wiki-plugins/wikiplugin_tracker.php');
			$vote = wikiplugin_tracker($data, $params);
			$smarty->assign_by_ref('vote', $vote);
		}
	} else {
		$smarty->assign('p_create_tracker_items', 'n');
	}
	if (isset($show_toggle) && $show_toggle == 'n') {
		$smarty->assign('show_toggle', 'n');
	}
	if (! isset($show_stat) || $show_stat == 'y') {
		$show_stat = 'y';
		if (isset($show_stat_only_after) && $show_stat_only_after == 'y') {
			if (! isset($options)) {
				$options = $trklib->get_tracker_options($trackerId);
				if (! empty($options['start']) || ! empty($options['end'])) {
					$smarty->assign_by_ref('options', $options);
				}
			}
			if (! empty($options['end']) && $tikilib->now < $options['end']) {
				$show_stat = 'n';
			}
		}
		if ($show_stat == 'y') {
			include_once('lib/wiki-plugins/wikiplugin_trackerstat.php');
			$stat = wikiplugin_trackerstat($data, $params);
			$smarty->assign_by_ref('stat', $stat);
		} else {
			$smarty->assign('stat', '');
		}
	} else {
		$smarty->assign('stat', '');
	}
	$smarty->assign('date', $tikilib->now);
	return $smarty->fetch('wiki-plugins/wikiplugin_vote.tpl');
}
