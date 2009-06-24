<?php

// Force compression disabling just for this script
// -> IE apparently doesn't handle gzip compression on javascript files
// (this is why FCKeditor doesn't find the "Tiki" toolbar defined here when compression is activated)
$force_no_compression = true;
include('tiki-setup.php');
include_once 'lib/quicktags/quicktagslib.php';

if ($prefs['feature_wysiwyg'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wysiwyg");
	$smarty->display("error.tpl");
	die;
}

$fckstyle = 'styles/'.$prefs['style'];
if ( $tikidomain and is_file('styles/'.$tikidomain.'/'.$prefs['style']) ) {
	$fckstyle = 'styles/'.$tikidomain.'/'.$prefs['style'];
}
$smarty->assign('fckstyle',$fckstyle);

$section = isset($_GET['section']) ? $_GET['section'] : 'wiki page';
$quicktags = QuicktagsList::fromPreference( $section );
file_put_contents('temp/cache/foo', print_r($quicktags->getWysiwygArray(), true));
$smarty->assign('toolbar', $quicktags->getWysiwygArray() );

$smarty->display('setup_fckeditor.tpl', null, null, 'application/javascript');
