<?php
//this script may only be included - so its better to die if called directly.
error_reporting(E_ALL);
global $access;
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $prefs;
if ($prefs['feature_ajax'] != 'y' ) {
	return;
}


$ajaxlib->registerFunction('WikiToHTML');

function WikiToHTML($data) {
	global $user,$tikilib;
	
	$options['absolute_links'] = true;
	$options['noparseplugins'] = false;
	$options['noheaderinc'] = true;
	$options['fck'] = 'y';
	$response =  new xajaxResponse('UTF-8');
	$content = $tikilib->parse_data($data,$options);
	$response->script("loadHTMLFromAjax('".preg_replace('/\n/',' ',$content)."')");

//	file_put_contents('/tmp/fckeditor_wiki.txt', $response->getOutput() );
	
	return $response;
}
