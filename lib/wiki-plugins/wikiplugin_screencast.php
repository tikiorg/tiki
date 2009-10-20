<?php
 
function wikiplugin_screencast_info() {
	return array(
		'name' => tra('Screencast'),
		'description' => tra('Display a screencast uploaded on the page.'),
		'prefs' => array( 'feature_wiki_screencasts', 'wikiplugin_screencast' ),
		'body' => tra('Label to use as a replacement to the default text'),
		'filter' => 'text',
		'params' => array(
			'file' => array(
				'required' => true,
				'name' => tra('File ID'),
				'description' => tra('ID as provided automatically during the upload.'),
				'filter' => 'url',
			),
		),
	);
}

function wikiplugin_screencast($data, $params) {
	global $locale, $prefs, $headerlib, $page, $screencastlib, $screencastCount, $cachelib;

	extract ($params, EXTR_SKIP);

	if (!$file )
		return tra("Screencast ID wasn't specified or doesn't exist");

	if ( !isset($screencastlib) || !is_object($screencastlib) )
		require_once("lib/screencasts/screencastlib.php"); 

	//Set header lib and add screencast js file
	if ( !isset($headerlib) || !is_object($headerlib) )
		require_once("lib/headerlib.php");

	//Set header lib and add screencast js file
	if ( !isset($cachelib) || !is_object($cachelib) )
		require_once("lib/cache/cachelib.php");

	$cachelib = new Cachelib();

	global $tikiroot;
	$headerlib->add_jsfile("{$tikiroot}lib/wikiplugin_screencast.js");

	$screencastCount++;
	$data = trim(htmlspecialchars($data));
	$file = htmlspecialchars($file);
	$msg = ($data) ? $data : tra("Watch a video of these instructions");

	$fileNameParts = split("-", $file);
	if ( $cachelib->isCached($fileNameParts[0]) ) {
		$allVideos = unserialize($cachelib->getCached($fileNameParts[0]));
	} else {
		$allVideos = $screencastlib->find($fileNameParts[0], true);
		$cachelib->cacheItem($fileNameParts[0], serialize($allVideos));
	}

	$videos = array();
	foreach( $allVideos as $f ) {
		if ( stripos($f, $file ) !== false )
			$videos[] = $f;
	}

	if ( !is_array($videos) || count($videos) < 1 )
		return tra("Screencast ID wasn't specified or doesn't exist");

	$videos = '["' . implode('","', $videos) . '"]';

	$html = '<div class="screencast-content-wrapper">';
	$html .= '<div class="screencast-content" id="' . $file . '">';
	$html .= '<div class="screencast-content-msg">' . $msg . '</div>';
	$html .= '</div>';
	$html .= '</div>';
	$html .= '<script type="text/javascript"> /*<![CDATA[*/ if ( typeof videos === "undefined" ) { videos = {}; } videos["' . $file . '"] = ' . $videos . '; /*]]>*/</script>';

	if ( $screencastCount == 1 )
		$html .= '<script type="text/javascript"> /*<![CDATA[*/ var screencastThumbText = "' . tra('Insert Screencast') . '" /*]]>*/</script>';

	return $html;    
}

