<?php

function prefs_feature_list() {
	return array(
		'feature_wiki' => array(
			'name' => tra('Wiki'),
			'description' => tra('Base wiki feature. Enabling it will open all wiki-related options and add options to the application menu.'),
			'type' => 'flag',
			'help' => 'Wiki',
		),
		'feature_blogs' => array(
			'name' => tra('Blogs'),
			'description' => tra('Enables the creation of multiple blogs in which the users can publish.'),
			'type' => 'flag',
			'help' => 'Blogs',
		),
		'feature_galleries' => array(
			'name' => tra('Image Galleries'),
			'description' => tra('Enables the creation of galleries in which pictures can be stored. Also see file galleries.'),
			'type' => 'flag',
			'help' => 'Image+Galleries',
		),
		'feature_machine_translation' => array(
			'name' => tra('Machine Translation (by Google Translate)'),
			'description' => tra('Uses Google Translate to translate the content of wiki pages to other languages.'),
			'help' => 'Translating+Tiki+Content',
			'warning' => tra('Experimental. This feature is still under development.'),
			'type' => 'flag',
		),
	);
}

?>
