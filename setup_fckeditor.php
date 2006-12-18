<?php
include 'tiki-setup.php';
header('Content-type: application/javascript');

$fckstyle = "styles/$style";
$smarty->assign('fckstyle',$fckstyle);

$tools = split("\r\n|\n",$wysiwyg_toolbar);
$line = 0;
$trim = create_function('&$x', '$x=trim($x);');
foreach ($tools as $t) {
	$t = trim($t);
	if ($t == '/') {
		$line++;
	} else {
		$els = split(',',$t);
		array_walk($els,$trim);
		$toolbar[$line][] = $els;
	}
}
$smarty->assign('toolbar',$toolbar);

$smarty->display('setup_fckeditor.tpl');
?>
