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
	global $user,$tikilib, $prefs;

	$options['absolute_links'] = true;
	$options['noparseplugins'] = false;
	$options['noheaderinc'] = true;
	$options['fck'] = 'y';
	$secedit = $prefs['wiki_edit_section'];
	$prefs['wiki_edit_section'] = 'n';    // get rid of the section edit icons
	$exticons = $prefs['feature_wiki_ext_icon'];
	$prefs['feature_wiki_ext_icon'] = 'n';    // and the external link icons
	$editplugin = $prefs['wiki_edit_plugin'];
	$prefs['wiki_edit_plugin'] = 'n';   // and the external link icons

	$response =  new xajaxResponse('UTF-8');

	// remove show/hide headings
	$data = preg_replace('/(!!*)[\+\-]/m','$1', $data);
	$content = $tikilib->parse_data($data,$options);
	// remove spans round img's
	$content = preg_replace('/<span class=\"img\">(.*?)<\/span>/im','$1', $content);
	$response->script("loadHTMLFromAjax('".addcslashes(urlencode($content),"'")."')");
	$prefs['wiki_edit_section'] = $secedit;
	$prefs['feature_wiki_ext_icon'] = $exticons;
	$prefs['wiki_edit_plugin'] = $editplugin;
	//file_put_contents('/tmp/fckeditor_wiki.txt', $response->getOutput() );

	return $response;
}
