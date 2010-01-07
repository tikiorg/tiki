<?php

// Force compression disabling just for this script
// -> IE apparently doesn't handle gzip compression on javascript files
// (this is why FCKeditor doesn't find the "Tiki" toolbar defined here when compression is activated)
$force_no_compression = true;
include('tiki-setup.php');
if ($prefs['feature_wysiwyg'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled') . ': feature_wysiwyg');
	$smarty->display('error.tpl');
	die;
}

include_once 'lib/toolbars/toolbarslib.php';

global $tikilib;
$smarty->assign('fckstyle',$tikilib->get_style_path('', '', $prefs['style']));
$smarty->assign('fckstyleoption',$tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']));

$section = isset($_GET['section']) ? $_GET['section'] : 'wiki page';

$toolbars = ToolbarsList::fromPreference( $section );
//file_put_contents('temp/cache/foo', print_r($toolbars->getWysiwygArray(), true));
$smarty->assign('toolbar', $toolbars->getWysiwygArray() );

$smarty->display('setup_fckeditor.tpl', null, null, 'application/javascript');
