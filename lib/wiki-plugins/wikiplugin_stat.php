<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_stat_info()
{
	return array(
		'name' => tra('Stat'),
		'documentation' => 'PluginStat',
		'description' => tra('Show various statistics for an object'),
		'prefs' => array('wikiplugin_stat'),
		'iconname' => 'chart',
		'introduced' => 4,
		'params' => array(
			'type' => array(
				'required' => true,
				'name' => tra('Object Type'),
				'description' => tra('Colon-separated list of object type to show stats for.'),
				'since' => '4.0',
				'filter' => 'text',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Article'), 'value' => 'article'), 
					array('text' => tra('Article & Tracker Item'), 'value' => 'article:trackeritem'),
					array('text' => tra('Article & Tracker Item & Wiki'), 'value' => 'article:trackeritem:wiki'),
					array('text' => tra('Article & Wiki'), 'value' => 'article:wiki'),
					array('text' => tra('Article & Wiki & Tracker Item'), 'value' => 'article:wiki:trackeritem'),
					array('text' => tra('Tracker Item'), 'value' => 'trackeritem'),
					array('text' => tra('Tracker Item & Article'), 'value' => 'trackeritem:article'),
					array('text' => tra('Tracker Item & Article & Wiki'), 'value' => 'trackeritem:article:wiki'),
					array('text' => tra('Tracker Item & Wiki'), 'value' => 'trackeritem:wiki'),
					array('text' => tra('Tracker Item & Wiki & Article'), 'value' => 'trackeritem:wiki:article'),
					array('text' => tra('Wiki'), 'value' => 'wiki'),
					array('text' => tra('Wiki & Article'), 'value' => 'wiki:article'),
					array('text' => tra('Wiki & Article & Tracker Item'), 'value' => 'wiki:article:trackeritem'),
					array('text' => tra('Wiki & Tracker Item'), 'value' => 'wiki:trackeritem'),
					array('text' => tra('Wiki & Tracker Item & Article'), 'value' => 'wiki:trackeritem:article'),
				)
			),
			'parentId' => array(
				'required' => false,
				'name' => tra('Parent ID'),
				'description' => tra('Enter a tracker ID to restrict stats to that tracker (for use with trackeritems only).'),
				'since' => '4.0',
				'filter' => 'digits',
				'profile_reference' => 'tracker',
			),
			'lastday' => array(
				'required' => false,
				'name' => tra('Last 24 Hours'),
				'description' => tr('Added and/or viewed in the last 24 hours (only added items shown for tracker
					items whether %0a%1 (added) or %0v%1 (viewed) or both is set)', '<code>', '</code>'),
				'since' => '4.0',
				'filter' => 'text',
				'accepted' => tra('a or v or both separated by a colon. Example: "a:v" or "v:a"'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Added'), 'value' => 'a'), 
					array('text' => tra('Added and Viewed'), 'value' => 'a:v'),
					array('text' => tra('Viewed'), 'value' => 'v'),
					array('text' => tra('Viewed & Added'), 'value' => 'v:a')
				)
			),
			'day' => array(
				'required' => false,
				'name' => tra('Today'),
				'description' => tr('Added and/or viewed since the beginning of the day (only added items shown for
					tracker items whether %0a%1 (added) or %0v%1 (viewed) or both is set)', '<code>', '</code>'),
				'since' => '4.0',
				'filter' => 'text',
				'accepted' => tra('a or v or both separated by a colon. Example: "a:v" or "v:a"'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Added'), 'value' => 'a'), 
					array('text' => tra('Added and Viewed'), 'value' => 'a:v'),
					array('text' => tra('Viewed'), 'value' => 'v'),
					array('text' => tra('Viewed & Added'), 'value' => 'v:a')
				)
			),
			'lastweek' => array(
				'required' => false,
				'name' => tra('Last 7 Days'),
				'description' => tr('Added and/or viewed in the last 7 days (only added items shown for tracker items
					whether %0a%1 (added) or %0v%1 (viewed) or both is set)', '<code>', '</code>'),
				'since' => '4.0',
				'filter' => 'text',
				'accepted' => tra('a or v or both separated by a colon. Example: "a:v" or "v:a"'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Added'), 'value' => 'a'), 
					array('text' => tra('Added and Viewed'), 'value' => 'a:v'),
					array('text' => tra('Viewed'), 'value' => 'v'),
					array('text' => tra('Viewed & Added'), 'value' => 'v:a')
				)
			),
			'week' => array(
				'required' => false,
				'name' => tra('This Week'),
				'description' => tr('Added and/or viewed since the beginning of the week (only added items shown for
					tracker items whether %0a%1 (added) or %0v%1 (viewed) or both is set)', '<code>', '</code>'),
				'since' => '4.0',
				'filter' => 'text',
				'accepted' => tra('a or v or both separated by a colon. Example: "a:v" or "v:a"'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Added'), 'value' => 'a'), 
					array('text' => tra('Added and Viewed'), 'value' => 'a:v'),
					array('text' => tra('Viewed'), 'value' => 'v'),
					array('text' => tra('Viewed & Added'), 'value' => 'v:a')
				)
			),
			'lastmonth' => array(
				'required' => false,
				'name' => tr('Last Month'),
				'description' => tr('Added and/or viewed last month (only added items shown for tracker items
					whether %0a%1 (added) or %0v%1 (viewed) or both is set)', '<code>', '</code>'),
				'since' => '4.0',
				'filter' => 'text',
				'accepted' => tra('a or v or both separated by a colon. Example: "a:v" or "v:a"'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Added'), 'value' => 'a'), 
					array('text' => tra('Added and Viewed'), 'value' => 'a:v'),
					array('text' => tra('Viewed'), 'value' => 'v'),
					array('text' => tra('Viewed & Added'), 'value' => 'v:a')
				)
			),
			'month' => array(
				'required' => false,
				'name' => tra('This Month'),
				'description' => tr('Added and/or viewed since the beginning of the month (only added items shown for
					tracker items whether %0a%1 (added) or %0v%1 (viewed) or both is set)', '<code>', '</code>'),
				'since' => '4.0',
				'filter' => 'text',
				'accepted' => tra('a or v or both separated by a colon. Example: "a:v" or "v:a"'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Added'), 'value' => 'a'), 
					array('text' => tra('Added and Viewed'), 'value' => 'a:v'),
					array('text' => tra('Viewed'), 'value' => 'v'),
					array('text' => tra('Viewed & Added'), 'value' => 'v:a')
				)
			),
			'lastyear' => array(
				'required' => false,
				'name' => tra('Last Year'),
				'description' => tr('Added and/or viewed in the last 365 days (only added items shown for tracker
					items whether %0a%1 (added) or %0v%1 (viewed) or both is set)', '<code>', '</code>'),
				'since' => '4.0',
				'filter' => 'text',
				'accepted' => tra('a or v or both separated by a colon. Example: "a:v" or "v:a"'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Added'), 'value' => 'a'), 
					array('text' => tra('Added and Viewed'), 'value' => 'a:v'),
					array('text' => tra('Viewed'), 'value' => 'v'),
					array('text' => tra('Viewed & Added'), 'value' => 'v:a')
				)
			),
			'year' => array(
				'required' => false,
				'name' => tra('This Year'),
				'description' => tr('Added and/or viewed since the beginning of the year (only added items shown for
					tracker items whether %0a%1 (added) or %0v%1 (viewed) or both is set)', '<code>', '</code>'),
				'since' => '4.0',
				'filter' => 'text',
				'accepted' => tra('a or v or both separated by a colon. Example: "a:v" or "v:a"'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Added'), 'value' => 'a'), 
					array('text' => tra('Added and Viewed'), 'value' => 'a:v'),
					array('text' => tra('Viewed'), 'value' => 'v'),
					array('text' => tra('Viewed & Added'), 'value' => 'v:a')
				)
			),
		),
	);
}

function wikiplugin_stat($data, $params) 
{
	$smarty = TikiLib::lib('smarty');
	$statslib = TikiLib::lib('stats');
	$stat = array();
	foreach ($params as $when=>$whats) {
		if ($when == 'type' || $when == 'parentId') {
			continue;
		}
		if (!in_array($when, array('day', 'lastday', 'week', 'lastweek', 'month', 'lastmonth', 'year', 'lastyear'))) {
			return tra('Incorrect parameter:').$when;
		}
		$whats = explode(':', $whats);
		$types = explode(':', $params['type']);
		foreach ($types as $type) {
			foreach ($whats as $what) {
				switch ($type) {
				case 'trackeritem':
					if ($what != 'v' && $what != 'a') {
						return tra('Incorrect parameter: ') . $what;
					}
					if (empty($params['parentId'])) {
						$params['parentId'] = 0;
					}
					//for tracker items, only added items can be shown, so eith a or v will result in added items being displayed
					$stat[$when][$type]['Added tracker items'] = $statslib->count_this_period('tiki_tracker_items', 'created', $when, 'trackerId', $params['parentId']);
    				break;
				case 'wiki':
					if ($what == 'v') {
						$stat[$when][$type]['Viewed wiki pages'] = $statslib->hit_this_period('wiki', $when);
					} elseif ($what == 'a') {
						$stat[$when][$type]['Added wiki pages'] = $statslib->count_this_period('tiki_pages', 'created', $when);
					} else {
						return tra('Incorrect parameter: ') . $what;
					}
    				break;
				case 'article':
					if ($what == 'v') {
						$stat[$when][$type]['Viewed articles'] = $statslib->hit_this_period('article', $when);
					} elseif ($what == 'a') {
						$stat[$when][$type]['Added articles'] = $statslib->count_this_period('tiki_articles', 'created', $when);
					} else {
						return tra('Incorrect parameter: ') . $what;
					}
    				break;
				default:
					return tra('Incorrect parameter: ').$type;
				}
			}
		}
	
	}
	$smarty->assign_by_ref('stat', $stat);
	$code = $smarty->fetch('wiki-plugins/wikiplugin_stat.tpl');
	return "~np~$code~/np~";
}
