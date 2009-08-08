<?php

// Force compression disabling just for this script
// -> IE apparently doesn't handle gzip compression on javascript files
// (this is why FCKeditor doesn't find the "Tiki" toolbar defined here when compression is activated)
$force_no_compression = true;
include('tiki-setup.php');
include_once 'lib/quicktags/quicktagslib.php';

$fckstyle = 'styles/'.$prefs['style'];
$smarty->assign('fckstyle',$fckstyle);

$section = isset($_GET['section']) ? $_GET['section'] : 'wiki page';
$quicktags = QuicktagsList::fromPreference( $section );

if ( $prefs['wysiwyg_htmltowiki'] == 'y' ) {
	$quicktags->insertTag('source', true);
}
if ( $prefs['feature_ajax_autosave'] == 'y' ) {
	$quicktags->insertTag('autosave', true);
}

error_reporting(E_ALL);

$toolbar = $quicktags->getWysiwygArray();
//file_put_contents('temp/cache/foo', print_r($toolbar, true));
$smarty->assign_by_ref('toolbar', $toolbar );
$smarty->display('setup_fckeditor.tpl', null, null, 'application/javascript');
