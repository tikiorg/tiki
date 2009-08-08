<?php

function prefs_feature_list() {
	return array(
		'feature_wiki' => array(
			'name' => tra('Wiki'),
			'description' => tra('Base wiki feature. Enabling it will open all wiki-related options and add options to the application menu.'),
			'type' => 'flag',
			'help' => 'Wiki',
		),
	);
}

?>
