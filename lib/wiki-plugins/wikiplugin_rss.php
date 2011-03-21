<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_rss_info() {
	return array(
		'name' => tra('RSS Feed'),
		'documentation' => 'PluginRSS',
		'description' => tra('Display items from an RSS feed'),
		'prefs' => array( 'wikiplugin_rss' ),
		'icon' => 'pics/icons/rss.png',
		'format' => 'html',
		'filter' => 'striptags',
		'params' => array(
			'id' => array(
				'required' => true,
				'name' => tra('IDs'),
				'separator' => ':',
				'filter' => 'int',
				'description' => tra('List of feed IDs separated by colons. ex: feedId:feedId2'),
				'default' => '',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Result Count'),
				'filter' => 'int',
				'description' => tra('Number of results displayed.'),
				'default' => 10,
			),
			'date' => array(
				'required' => false,
				'name' => tra('Date'),
				'filter' => 'int',
				'description' => tra('Show date of each item (not shown by default)'),
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
				'filter' => 'int',
				'description' => tra('Show feed descriptions (not shown by default)'),
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
				'filter' => 'int',
				'description' => tra('Show authors (not shown by default)'),
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
				'default' => '',
			),
			'showtitle' => array(
				'required' => false,
				'name' => tra('Show Title'),
				'filter' => 'int',
				'description' => tra('Show the title of the feed (shown by default)'),
				'default' => 1,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				)
			),
		),
	);
}

function wikiplugin_rss($data,$params) {
	global $smarty;
	global $rsslib; require_once 'lib/rss/rsslib.php';

	$params = array_merge( array(
		'max' => 10,
		'date' => 0,
		'desc' => 0,
		'author' => 0,
		'icon' => '',
                'showtitle' => 1,
	), $params );

	if ( ! isset( $params['id'] ) ) {
		return WikiParser_PluginOutput::argumentError( array( 'id' ) );
	}

	$params['id'] = (array) $params['id'];

	$items = $rsslib->get_feed_items( $params['id'], $params['max'] );

	$title = null;
	if( count( $params['id'] ) == 1 ) {
		$module = $rsslib->get_rss_module( reset( $params['id'] ) );

		if( $module['sitetitle'] ) {
			$title = array(
				'title' => $module['sitetitle'],
				'link' => $module['siteurl'],
			);
		}
	}

	global $smarty;
	$smarty->assign( 'title', $title );
	$smarty->assign( 'items', $items );
	$smarty->assign( 'showdate', $params['date'] > 0 );
	$smarty->assign( 'showtitle', $params['showtitle'] > 0 );
	$smarty->assign( 'showdesc', $params['desc'] > 0 );
	$smarty->assign( 'showauthor', $params['author'] > 0 );
	$smarty->assign( 'icon', $params['icon'] );
	return $smarty->fetch( 'wiki-plugins/wikiplugin_rss.tpl' );
}

