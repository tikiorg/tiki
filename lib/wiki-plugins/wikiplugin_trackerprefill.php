<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_trackerprefill.php,v 1.1.2.1 2008-02-04 14:01:05 sylvieg Exp $
function wikiplugin_trackerprefill_help() {
	$help = tra('Displays a button to link to a page with a tracker plugin with prefilled tracker fields.');
	$help .= '~np~{TRACKERPREFILL(page=trackerpage,label=text,field1=id,value1=, field2=id,value2=... /)}';
	return $help;
}
function wikiplugin_trackerprefill($data, $params) {
	global $smarty;
	$prefills = array();
	foreach ($params as $param=>$value) {
		if (strstr($param, 'field')) {
			$id = substr($param, strlen('field'));
			$f['fieldId'] = $value;
			$f['value'] = $params["value$id"];
			$prefills[] = $f;
		}
	}
	$smarty->assign_by_ref('prefills', $prefills);
	$smarty->assign_by_ref('params', $params);
	return $smarty->fetch('wiki-plugins/wikiplugin_trackerprefill.tpl');
}