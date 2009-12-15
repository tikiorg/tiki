<?php
// $Id$
//
// Called by FCKEditor and defined in setup_fckeditor.tpl - FCKConfig.ajaxAutoSaveTargetUrl

$inputConfiguration = array( array(
	'staticKeyFilters' => array(
		'editor_id' => 'alpha',
//		'data' => 'alpha',
//		'script' => 'alpha',
	),
) );

require_once('tiki-setup.php');

if ($prefs['feature_ajax'] != 'y' || $prefs['feature_ajax_autosave'] != 'y') {
	return;
}

require_once('lib/ajax/ajaxlib.php');

if (isset($_REQUEST['editor_id']) and isset($_REQUEST['data'])) {
	auto_save($_REQUEST['editor_id'],$_REQUEST['data'],$_REQUEST['script']);
	header( 'Content-Type:text/xml; charset=UTF-8' ) ;
	echo '<?xml version="1.0" encoding="UTF-8"?>';
	echo '<adapter command="draft">';
	echo '<result message="success" />';
	echo '</adapter>';
}
