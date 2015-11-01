<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_rss_info()
{
	return array(
		'name' => tra('RSS Feed'),
		'documentation' => 'PluginRSS',
		'description' => tra('Display items from one or more RSS feeds'),
		'prefs' => array( 'wikiplugin_rss' ),
		'iconname' => 'rss',
		'introduced' => 1,
		'format' => 'html',
		'filter' => 'striptags',
		'tags' => array( 'basic' ),
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('IDs'),
				'separator' => ':',
				'filter' => 'int',
				'description' => tr('List of feed IDs separated by colons. ex: %0', '<code>feedId:feedId2</code>'),
				'since' => '1',
				'default' => '',
				'profile_reference' => 'rss',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Result Count'),
				'filter' => 'int',
				'description' => tra('Number of results displayed.'),
				'since' => '1',
				'default' => 10,
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'filter' => 'digits',
				'description' => tra('Show date of each item (not shown by default)'),
				'since' => '1',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'desc' => array(
				'required' => false,
				'name' => tra('Description'),
				'filter' => 'digits',
				'description' => tra('Show feed descriptions (not shown by default)'),
				'since' => '1',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'author' => array(
				'required' => false,
				'name' => tra('Author'),
				'filter' => 'digits',
				'description' => tra('Show authors (not shown by default)'),
				'since' => '1',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'icon' => array(
				'required' => false,
				'name' => tra('Icon'),
				'filter' => 'url',
				'description' => tra('Url to a favicon to put before each entry'),
				'since' => '5.0',
				'default' => '',
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'filter' => 'digits',
				'description' => tra('Show the title of the feed (shown by default)'),
				'since' => '6.0',
				'default' => 1,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'ticker' => array(
				'required' => false,
				'name' => tra('Ticker'),
				'filter' => 'digits',
				'description' => tra('Turn static feed display into ticker news like'),
				'since' => '10.1',
				'default' => 1,
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 1),
					array('text' => tra('No'), 'value' => 0)
				)
			),
			'desclen' => array(
				'required' => false,
				'name' => tra('Description Length'),
				'filter' => 'digits',
				'description' => tra('Max characters/length, truncates text to fit design'),
				'since' => '10.1',
				'default' => 0,
			),
		),
	);
}

function wikiplugin_rss($data,$params)
{
	$rsslib = TikiLib::lib('rss');

	$params = array_merge(
		array(
			'max' => 10,
			'date' => 0,
			'desc' => 0,
			'author' => 0,
			'icon' => '',
			'showtitle' => 1,
			'ticker' => 0,
			'desclen' => 0,
		),
		$params
	);

	if ( ! isset( $params['id'] ) ) {
		return WikiParser_PluginOutput::argumentError(array( 'id' ));
	}

	$params['id'] = (array) $params['id'];

	$items = $rsslib->get_feed_items($params['id'], $params['max']);

	$title = null;
	if ( count($params['id']) == 1 ) {
		$module = $rsslib->get_rss_module(reset($params['id']));

		if ( $module['sitetitle'] ) {
			$title = array(
				'title' => $module['sitetitle'],
				'link' => $module['siteurl'],
			);
		}
	}

	if ($params['desc'] > 0 && $params['desclen'] > 0) {
		foreach ($items as &$item) {
			$item['description'] = limit_text($item['description'], $params['desclen']);
		}
	}

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('rsstitle', $title);
	$smarty->assign('items', $items);
	$smarty->assign('showdate', $params['date'] > 0);
	$smarty->assign('showtitle', $params['showtitle'] > 0);
	$smarty->assign('showdesc', $params['desc'] > 0);
	$smarty->assign('showauthor', $params['author'] > 0);
	$smarty->assign('icon', $params['icon']);
	$smarty->assign('ticker', $params['ticker']);
	return $smarty->fetch('wiki-plugins/wikiplugin_rss.tpl');
}

function limit_text($text, $limit)
{
	if (str_word_count($text, 0) > $limit) {
		$words = str_word_count($text, 2);
		$pos = array_keys($words);
		$text = substr($text, 0, $pos[$limit]) . '...';
	}
	return $text;
}
