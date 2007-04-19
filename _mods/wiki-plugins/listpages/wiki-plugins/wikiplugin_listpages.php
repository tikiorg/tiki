<?php
// $Header: /cvsroot/tikiwiki/_mods/wiki-plugins/listpages/wiki-plugins/wikiplugin_listpages.php,v 1.3 2007-04-19 16:22:47 sylvieg Exp $
function wikiplugin_listpages_help() {
	return tra("List pages.")."<br />~np~{LISTPAGES(initial=txt,showNameOnly=y,categId=id,structHead=y,showPageAlias=y)}{LISTPAGES}~/np~";
}
function wikiplugin_listpages($data, $params) {
	global $feature_listPages, $tiki_p_view, $tikilib, $smarty;
	if ($feature_listPages != 'y' || $tiki_p_view != 'y') {
		return '';
	} 
	extract($params,EXTR_SKIP);
	$filter == array();
	if (!isset($initial)) {
		if (isset($_REQUEST['initial'])) {
			$initial = $_REQUEST['initial'];
		} else {
			$initial = '';
		}
	}
	if (!empty($categId)) {
		$filter['categId'] = $categId;
	}
	if (!empty($structHead) && $structHead == 'y') {
		$filter['structHead'] = $structHead;
	}
	if (!isset($offset)) {
		$offset = 0;
	}
	if (!isset($max)) {
		$max = -1;
	}
	$listpages = $tikilib->list_pages($offset, $max, 'pageName_asc', '', $initial, true, (isset($showOnlyName)&& $showOnlyName == 'y')? true: false, false, false, $filter);
	$smarty->assign_by_ref('listpages', $listpages['data']);
	if (!empty($showPageAlias) && $showPageAlias == 'y')
		$smarty->assign_by_ref('showPageAlias', $showPageAlias);
	if (isset($showNameOnly) && $showNameOnly == 'y') {
		$ret = $smarty->fetch('wiki-plugins/wikiplugin_listpagenames.tpl');
	} else {
		$ret = $smarty->fetch('tiki-listpages_content.tpl');
	}
	return $ret;
}
?>