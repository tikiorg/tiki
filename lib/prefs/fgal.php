<?php

function prefs_fgal_list() {
	return array(
		'fgal_podcast_dir' => array(
			'name' => tra('Podcast directory'),
			'type' => 'text',
			'help' => 'File+Gallery+Config',
			'size' => 50,
			'hint' => tra('The server must be able to read/write the directory.').' '.tra('Required for podcasts.'),
		),
		'fgal_use_dir' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'help' => 'File+Gallery',
		),
		'fgal_batch_dir' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'help' => 'File+Gallery+config',
			'size' => 50,
			'hint' => tra('If you enable Directory Batch Loading, you need to setup a web-readable directory (outside of your web space is better). Then setup a way to upload files in that dir, either by scp, ftp, or other protocols').' '.tra('The server must be able to read the directory.').' '. tra('The directory can be outside the web space.'),
		),
		'fgal_prevent_negative_score' => array(
			'name' => tra('Prevent download if score becomes negative'),
			'type' => 'text',
			'help' => 'File+Gallery+Config',
			'size' => 50,
		),
		'fgal_limit_hits_per_file' => array(
			'name' => tra('Allow download limit per file'),
			'type' => 'flag',
			'help' => 'File+Gallery+Config',
		),
		'fgal_prevent_negative_score' => array(
			'name' => tra('Prevent download if score becomes negative'),
			'type' => 'flag',
			'help' => 'File+Gallery+Config',
		),
		'fgal_allow_duplicates' => array(
			'name' => tra('Allow same file to be uploaded more than once'),
			'type' => 'list',
			'help' => 'File+Gallery+Config',
			'options' => array(
							  'n' => tra('Never'),
							  'y' => tra('Yes, even in the same gallery'),
							  'different_galleries' => tra('Only in different galleries')
							  ),
		),
		'fgal_match_regex' => array(
			'name' => tra('Must match'),
			'type' => 'text',
			'size' => 50,
		),
		'fgal_nmatch_regex' => array(
			'name' => tra('Cannot match'),
			'type' => 'text',
			'size' => 50,
		),
		'fgal_quota' => array (
			'name' => tra('Quota for all the files and archives'),
			'shorthint' => tra('Mb').' '.tra('(0 for illimitted)'),
			'type' => 'text',
			'size' => 7,
		),
		'fgal_quota_per_fgal' => array (
			'name' => tra('Quota can be defined for each file gallery'),
			'type' => 'flag',
		),
		'fgal_quota_default' => array (
			'name' => tra('Default quota for each new gallery'),
			'shorthint' => tra('Mb').' '.tra('(0 for illimitted)'),
			'type' => 'text',
			'size' => 7,
		),
		'fgal_quota_show' => array (
			'name' => tra('Show quota bar in the list page'),
			'type' => 'flag',
		),
		/*
		'fgal_use_db' => array(
			'type' => 'radio',
			'options' => array(
				'n' => tra('Store in directory'),
				'y' => tra('Store in database'),
			),
		),
		'fgal_use_dir' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'size' => 50,
		),
		*/
	);
}