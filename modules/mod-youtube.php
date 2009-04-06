<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $smarty;
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_YouTube');

// Module special params:
// - user: YouTube user, to display a link to the videos of this user
// - ids: comma separated list of YouTube videos ids to display
// - width
// - height

$data = array(
	'urls' => array(),
	'xhtml' => array()
);

if ( !empty($module_params['ids']) ) {
	require_once('lib/wiki-plugins/wikiplugin_youtube.php');
	$ids = explode(',', $module_params['ids']);
	$data['urls']['gdata'] = array();
	foreach ( $ids as $id ) {
		$data['urls']['gdata'][$id] = Zend_Gdata_YouTube::VIDEO_URI . '/' . $id;
		$params = array('movie' => $id);
		if ( isset($module_params['width']) ) $params['width'] = $module_params['width'];
		if ( isset($module_params['height']) ) $params['height'] = $module_params['height'];
		$data['xhtml'][$id] = wikiplugin_youtube('', $params);
	}
}

if ( !empty($module_params['user']) ) {
	$data['urls']['user_home'] = 'http://www.youtube.com/user/' . $module_params['user'];
}

$smarty->assign_by_ref('data', $data);
