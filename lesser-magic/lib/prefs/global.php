<?php

function prefs_global_list() {
	return array(
		'browsertitle' => array(
			'name' => tra('Browser title'),
			'description' => tra('Label visible in the browser\'s title bar on all pages. Also appears in search engines.'),
			'type' => 'text',
		),
	);
}

?>
