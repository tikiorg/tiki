<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (isset($module_params['pagemenu'])) {
	$pagemenu = $module_params['pagemenu'];
} elseif (isset($module_params['page'])) {
	$pagemenu = $module_params['page'];
}

if (!empty($pagemenu)) {
	$info = $tikilib->get_page_info($pagemenu);
	if (!empty($info)) {
		$content = $tikilib->parse_data($info['data'], array('is_html' => $info['is_html']));
		$smarty->assign('module_title', isset($module_params['title']) ? $module_params['title'] : $pagemenu);
		$smarty->assign_by_ref('contentmenu',$content);
	}
}

?>
