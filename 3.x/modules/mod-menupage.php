<?php
// $Id$
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $smarty;
if (isset($module_params['pagemenu'])) {
	$pagemenu = $module_params['pagemenu'];
} elseif (isset($module_params['page'])) {
	$pagemenu = $module_params['page'];
}

if (!empty($pagemenu)) {
	global $wikilib; include_once('lib/wiki/wikilib.php');
	$content = $wikilib->get_parse($pagemenu, $canBeRefreshed);
	$smarty->assign('module_title', isset($module_params['title']) ? $module_params['title'] : $pagemenu);
	$smarty->assign_by_ref('contentmenu',$content);
}
