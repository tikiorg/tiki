<?php
    require_once('../../tiki-setup.php');

	$smarty->assign('mid',PKG_WEATHER_PATH.'/weather.tpl');
	$smarty->display("tiki.tpl");
	
?>

