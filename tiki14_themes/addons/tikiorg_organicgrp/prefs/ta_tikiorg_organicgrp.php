<?php

// Warning: pref names have a limit of 40 characters
// Please prefix with ta_ (short for tikiaddon) and your package vendor and name

function prefs_ta_tikiorg_organicgrp_list()
{
	return array(
		'ta_tikiorg_organicgrp_on' => array( // This main _on pref is mandatory
			'name' => tra('Turn on Tiki Organic Groups'),
			'description' => tra('Activate Tiki Organic Groups Addon'),
			'type' => 'flag',
			'admin' => 'ta_tikiorg_organicgrp',
			'tags' => array('basic'),
			'default' => 'y',
		),
		'ta_tikiorg_organicgrp_sterm' => array(
			'name' => tra('Term to use for organic groups (singular)'),
			'description' => tra('This is the term to use to describe organic groups, could be Team or whatever.'),
			'type' => 'text',
			'size' => '20',
			'tags' => array('basic'),
			'default' => 'Group',
			'public' => 'true',
		),
		'ta_tikiorg_organicgrp_pterm' => array(
			'name' => tra('Term to use for organic groups (plural)'),
			'description' => tra('This is the plural term to use to describe organic groups, could be Teams or whatever.'),
			'type' => 'text',
			'size' => '20',
			'tags' => array('basic'),
			'default' => 'Groups',
			'public' => 'true',
		),
		'ta_tikiorg_organicgrp_listprivate' => array(
			'name' => tra('Show private groups in public list'),
			'description' => tra('Show private groups in public list so people can request to join.'),
			'type' => 'flag',
			'tags' => array('basic'),
			'default' => 'y',
		),
	);
}