<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_metatag_list() {
	return array(
		'metatag_keywords' => array(
			'name' => tra('Keywords'),
			'type' => 'textarea',
			'size' => '4',
			'default' => '',
			'tags' => array('basic'),
		),
		'metatag_freetags' => array(
			'name' => tra('Include freetags'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_freetags',
			),
			'default' => 'n',
		),
		'metatag_threadtitle' => array(
			'name' => tra('Use thread title instead'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_forums',
			),
			'default' => 'n',
		),
		'metatag_imagetitle' => array(
			'name' => tra('Use image title instead'),
			'type' => 'flag',
			'dependencies' => array(
				'feature_galleries',
			),
			'default' => 'n',
		),
		'metatag_description' => array(
			'name' => tra('Description'),
			'type' => 'textarea',
			'size' => '5',
			'default' => '',
			'tags' => array('basic'),
		),
		'metatag_pagedesc' => array(
			'name' => tra('Use individual page description for Wiki pages instead'),
			'type' => 'flag',
			'default' => 'n',
		),
		'metatag_author' => array(
			'name' => tra('Author'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
			'tags' => array('basic'),
		),
		'metatag_geoposition' => array(
			'name' => tra('geo.position'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'metatag_georegion' => array(
			'name' => tra('geo.region'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'metatag_geoplacename' => array(
			'name' => tra('geo.placename'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'metatag_robots' => array(
			'name' => tra('Meta robots'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'metatag_revisitafter' => array(
			'name' => tra('Revisit after'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
	);
}
