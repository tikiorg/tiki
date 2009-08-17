<?php

// Force compression disabling just for this script
// -> IE apparently doesn't handle gzip compression on javascript files
// (this is why FCKeditor doesn't find the "Tiki" toolbar defined here when compression is activated)
$force_no_compression = true;
include('tiki-setup.php');
include_once 'lib/toolbars/toolbarslib.php';

$fckstyle = 'styles/'.$prefs['style'];
$smarty->assign('fckstyle',$fckstyle);

$section = isset($_GET['section']) ? $_GET['section'] : 'wiki page';

$toolbars = ToolbarsList::fromPreference( $section );
file_put_contents('temp/cache/foo', print_r($toolbars->getWysiwygArray(), true));
$smarty->assign('toolbar', $toolbars->getWysiwygArray() );

$smarty->display('setup_fckeditor.tpl', null, null, 'application/javascript');
