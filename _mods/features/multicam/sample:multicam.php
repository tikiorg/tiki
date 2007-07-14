<?php
include "tiki-setup.php";

$data = $tikilib->get_page_info("[:::[webcampage]:::]");
$meat = $data['data'];
$meat = substr($meat,strpos($meat,"||")+2);
$meat = substr($meat,0,strrpos($meat,"||")-1);
$m = split("\r\n",$meat);
foreach ($m as $me) {
	$v = split('\|',$me);
	$w = trim($v[0]);
	$cams[$w] = trim($v[1]);
	$cam_info[$w] = trim($v[2]);
	$cam_loc[$w] = trim($v[3]);
}

$smarty->assign('cams',$cams);
$smarty->assign('cam_info',$cam_info);
$smarty->assign('cam_loc',$cam_loc);
$smarty->assign('mid','multicam.tpl');
$smarty->display('tiki.tpl');
?>
