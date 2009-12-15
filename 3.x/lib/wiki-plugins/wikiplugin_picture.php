<?php

function wikiplugin_picture_info()
{
	return array(
		'name' => tra('Picture'),
		'documentation' => 'PluginPicture',	
		'description' => tra('Search for images uploaded by users').tra(' (deprecated - scheduled to be removed or changed significantly)'),
		'prefs' => array( 'feature_wiki_pictures', 'wikiplugin_picture' ),
		'params' => array(
			'file' => array(
				'name' => tra('File'),
				'description' => tra('Filename or full path to file'),
				'required' => true,
			),
		),
	);
}

function wikiplugin_picture( $data, $params )
{
	global $tikidomain;

	if( ! isset( $params['file'] ) )
		return;

	// Check if the image exists
	$name = $params['file'];
	if ($tikidomain && !preg_match('|^https?:|', $name)) {
		$name = preg_replace("~img/wiki_up/~","img/wiki_up/$tikidomain/",$name);
	}

	if (file_exists($name) and (preg_match('/(gif|jpe?g|png)$/i',$name))) {
		// Replace by the img tag to show the image
		$repl = "<span class='img'><img src='$name' alt='$name' /></span>";
	} else {
		$repl = tra('picture not found')." $name";
	}

	return $repl;
}

?>
