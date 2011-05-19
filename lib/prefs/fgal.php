<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_fgal_list() {
	return array(
		'fgal_podcast_dir' => array(
			'name' => tra('Podcast directory'),
			'type' => 'text',
			'help' => 'File+Gallery+Config',
			'size' => 50,
			'hint' => tra('The server must be able to read/write the directory.').' '.tra('Required for podcasts.'),
			'perspective' => false,
			'default' => 'files/',
		),
		'fgal_use_dir' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'help' => 'File+Gallery',
			'perspective' => false,
			'default' => '',
		),
		'fgal_batch_dir' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'help' => 'File+Gallery+config',
			'size' => 50,
			'hint' => tra('If you enable Directory Batch Loading, you need to setup a web-readable directory (outside of your web space is better). Then setup a way to upload files in that dir, either by scp, ftp, or other protocols').' '.tra('The server must be able to read the directory.').' '. tra('The directory can be outside the web space.'),
			'perspective' => false,
			'default' => '',
		),
		'fgal_prevent_negative_score' => array(
			'name' => tra('Prevent download if score becomes negative'),
			'type' => 'text',
			'help' => 'File+Gallery+config',
			'size' => 50,
			'default' => 'n',
		),
		'fgal_limit_hits_per_file' => array(
			'name' => tra('Allow download limit per file'),
			'type' => 'flag',
			'help' => 'File+Gallery+config',
			'default' => 'n',
		),
		'fgal_allow_duplicates' => array(
			'name' => tra('Allow same file to be uploaded more than once'),
			'type' => 'list',
			'help' => 'File+Gallery+config',
			'perspective' => false,
			'options' => array(
							  'n' => tra('Never'),
							  'y' => tra('Yes, even in the same gallery'),
							  'different_galleries' => tra('Only in different galleries')
			),
			'default' => 'y',
		),
		'fgal_display_zip_option' => array(
			'name' => tra('Display zip option in gallery'),
			'type' => 'flag',
			'description' => tra('Display in the gallery the zip option (in upload and gallery file)'),
			'help' => 'File+Gallery+config',
			'default' => 'y',
		),
		'fgal_upload_progressbar' => array(
			'name' => tra('Upload progressbar'),
			'type' => 'list',
			'options'=> array(
				'n'	=>	tra('None'),
				'ajax_flash' => tra('Ajax / Flash (Browser-based)'),
			),
			'help' => 'File+Gallery+config',
			'default' => 'n',
		),
		'fgal_match_regex' => array(
			'name' => tra('Must match'),
			'type' => 'text',
			'size' => 50,
			'default' => '',
		),
		'fgal_nmatch_regex' => array(
			'name' => tra('Cannot match'),
			'type' => 'text',
			'size' => 50,
			'default' => '',
		),
		'fgal_quota' => array (
			'name' => tra('Quota for all the files and archives'),
			'shorthint' => tra('Mb').' '.tra('(0 for unlimited)'),
			'type' => 'text',
			'size' => 7,
			'default' => 0,
		),
		'fgal_quota_per_fgal' => array (
			'name' => tra('Quota can be defined for each file gallery'),
			'type' => 'flag',
			'default' => 'n',
		),
		'fgal_quota_default' => array (
			'name' => tra('Default quota for each new gallery'),
			'shorthint' => tra('Mb').' '.tra('(0 for unlimited)'),
			'type' => 'text',
			'size' => 7,
			'default' => 0,
		),
		'fgal_quota_show' => array (
			'name' => tra('Show quota bar in the list page'),
			'type' => 'list',
			'options' => array(
							  'n' 				=> tra('Never'),
							  'bar_and_text' 	=> tra('Yes, display bar and detail text'),
							  'y' 				=> tra('Yes, display only bar'),
							  'text_only'		=> tra('Yes, display only text')
			),
			'default' => 'y',
		),
		'fgal_use_db' => array(
			'name' => tra('Storage'),
			'type' => 'list',
			'perspective' => false,
			'options' => array(
				'y' => tra('Store in database'),
				'n' => tra('Store in directory'),
			),
			'default' => 'y',
		),
		'fgal_use_dir' => array(
			'name' => tra('Path'),
			'type' => 'text',
			'size' => 50,
			'perspective' => false,
		),
		'fgal_search_in_content' => array(
			'name' => tra('Include the search box on the current gallery files just after the find div'),
			'type' => 'flag',
			'default' => 'n',
		),
		'fgal_search' => array(
			'name' => tra('Include a search box on file galleries'),
			'type' => 'flag',
			'default' => 'y',
		),
		'fgal_list_ratio_hits' => array(
			'name' => tra('Display hits with a ratio between hits / maxhits'),
			'type' => 'flag',
		),
		'fgal_display_properties' => array(
			'name' => tra('Display properties in the context menu'),
			'type' => 'flag',
			'default' => 'y',
		),
		'fgal_display_replace' => array(
			'name' => tra('Display replace menu in context menu'),
			'type' => 'flag',
			'default' => 'y',
		),
		'fgal_delete_after' => array(
			'name' => tra('Automatic deletion of old files'),
			'description' => tra('The user will have an option when uploading a file to specify the time after which the file is deleted'),
			'type' => 'flag',
			'help' => 'File+Gallery+Config',
			'default' => 'n',
		),
		'fgal_checked' => array(
			'name' => tra('Allow action on multiple files or galleries'),
			'description' => tra('The check button on file gallery can be remove'),
			'type' => 'flag',
			'help' => 'File+Gallery+Config',
			'default' => 'y',
		),
		'fgal_delete_after_email' => array(
			'name' => tra('Deletion emails notification'),
			'description' => tra('These emails will receive a copy of each deleted file. Emails are separated with comma'),
			'type' => 'text',
			'default' => '',
		),
		'fgal_keep_fileId' => array(
			'name' => tra('Keep fileId for archives'),
			'description' => tra('Keep always the same fileId when replacing a file with archive'),
			'type' => 'flag',
			'default' => 'n',
		),
		'fgal_show_thumbactions' => array(
			'name' => tra('Show thumbnail actions'),
			'description' => tra('Show the checkbox and wrench icon for file actions menu when not displaying details'),
			'type' => 'flag',
			'default' => 'y',
		),
		'fgal_thumb_max_size' => array (
			'name' => tra('Max thumbnail size'),
			'description' => tra('Maximum width or height for image thumbnails'),
			'shorthint' => tra('In pixels'),
			'type' => 'text',
			'size' => 5,
			'default' => 120,
		),
	);
}
