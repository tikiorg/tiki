<?php
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-last_category_objects.php,v 1.2 2004-02-29 17:38:49 mose Exp $

if (!isset($module_params["type"])) {
	$module_params["type"] = "wiki page";
}
if ($module_params["type"] == '*') {
	$module_params["type"] = '';
}
if (!isset($module_params["id"])) {
	$last["data"][]['name'] = tra("Please provide an Id");
} else {
	$last = $tikilib->last_category_objects($module_params["id"],$module_rows,$module_params["type"]);
}
if (!is_array($last) or !is_array($last['data'])) {
	$last['data'][]['name'] = tra("no object here yet");
}
$smarty->assign('last',$last['data']);
$smarty->assign('type',$module_params["type"]);
?>
