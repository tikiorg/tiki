<?php
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($module_params['page'])) {
	if ($tikilib->page_exists($module_params['page'])) {
		$info = $tikilib->get_page_info($module_params['page']);
		$content = $tikilib->parse_data($info['data']);
		$smarty->assign('tpl_module_title',$module_params['page']);
		$smarty->assign('contentmenu',$content);
	}
}

?>
