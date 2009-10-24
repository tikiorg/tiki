<?php

function prefs_fgal_list() {
	return array(
		'fgal_podcast_dir' => array(
			'name' => tra('Podcast directory'),
			'type' => 'text',
			'help' => 'File+Gallery+Config',
			'size' => 50,
		),
		'fgal_use_dir' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'help' => 'File+Gallery',
		),
		'fgal_batch_dir' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'help' => 'File+Gallery',
			'size' => 50,
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
		),	);
}