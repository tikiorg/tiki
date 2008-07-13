<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($module_params['pagemenu'])) {
	if ($tikilib->page_exists($module_params['pagemenu'])) {
		$info = $tikilib->get_page_info($module_params['pagemenu']);
		$content = $tikilib->parse_data($info['data'], $info['is_html']);
		$smarty->assign('tpl_module_title',$module_params['pagemenu']);
		$smarty->assign('contentmenu',$content);
	}
}

?>
