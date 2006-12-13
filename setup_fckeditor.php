<?php
include 'tiki-setup.php';
header('Content-type: application/javascript');

if (is_file("lib/fckeditor_tiki/styles/fck-$style")) {
	$fckstyle = "lib/fckeditor_tiki/styles/fck-$style";
} else {
	$fckstyle = "styles/$style";
}
$smarty->assign('fckstyle',$fckstyle);

$smarty->display('setup_fckeditor.tpl');
?>
