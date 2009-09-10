<?php

// Force compression disabling just for this script
// -> IE apparently doesn't handle gzip compression on javascript files
// (this is why FCKeditor doesn't find the "Tiki" toolbar defined here when compression is activated)
$force_no_compression = true;
include('tiki-setup.php');

if ($prefs['feature_wysiwyg'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_wysiwyg");
	$smarty->display("error.tpl");
	die;
}

global $tikilib;
$smarty->assign('fckstyle',$tikilib->get_style_path('', '', $prefs['style']));
$smarty->assign('fckstyleoption',$tikilib->get_style_path($prefs['style'], $prefs['style_option'], $prefs['style_option']));

$tools = split("\r\n|\n",$prefs['wysiwyg_toolbar']);
$line = 0;
foreach ($tools as $t) {
	$t = trim($t);
	if ($t == '/') {
		$line++;
	} else {
		$els = split(',',$t);
		$els = array_map('trim',$els);
		$toolbar[$line][] = $els;
	}
}
$smarty->assign('toolbar',$toolbar);

$smarty->display('setup_fckeditor.tpl', null, null, 'application/javascript');
?>
