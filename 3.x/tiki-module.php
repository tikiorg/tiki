<?php 
// $Id: /cvsroot/tikiwiki/tiki/categorize.php,v 1.25.2.1 2007-11-27 18:06:49 nkoth Exp $
//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
	global $smarty, $tikilib;
	if (isset($module_params['title'])) { 
		$smarty->assign('tpl_module_title',tra($module_params['title'])); 
	}
	$template = 'modules/mod-' . $mod_reference['name'] . '.tpl';
	$phpfile = 'modules/mod-' . $mod_reference['name'] . '.php';
	if (!$mod_reference['rows']) {
		$mod_reference['rows'] = 10;
	}
	$smarty->assign_by_ref('module_rows',$mod_reference['rows']);
	$smarty->assign_by_ref('module_params', $module_params); // module code can unassign this if it wants to hide params
	$smarty->assign('module_ord', $mod_reference['ord']);
	$smarty->assign('module_position', $mod_reference['position']);
	$smarty->assign('moduleId', $mod_reference['moduleId']);
	if (file_exists($phpfile)) {
		include ($phpfile);
	}
	if (file_exists('templates/'.$template)) {
		$data = $smarty->fetch($template);
	} else {
		$info = $tikilib->get_user_module($mod_reference['name']);
		if (!empty($info)) {
			// test if we have a menu
			if (strpos($info['data'],'{menu ') === 0 
					and strpos($info['data'],"css=y")) {
				$smarty->assign('module_type','cssmenu');
			} else {
				$smarty->assign('module_type','module');
			}
			$smarty->assign('user_title', tra($info['title']));
			if (isset($info['parse']) && $info['parse'] == 'y')
				$info['data'] = $tikilib->parse_data($info['data']);
			$smarty->assign_by_ref('user_data', $info['data']);
			$smarty->assign_by_ref('user_module_name', $info['name']);
			$data = $smarty->fetch('modules/user_module.tpl');
		} else {
			$data = '';
		}
	}
	$smarty->clear_assign('module_params'); // ensure params not available outside current module
	$smarty->clear_assign('tpl_module_title');
