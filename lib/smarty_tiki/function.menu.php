<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_menu($params, &$smarty)
{
    global $tikilib, $user, $headerlib, $prefs;
    global $menulib; include_once('lib/menubuilder/menulib.php');
	extract($params);

	if (empty($link_on_section) || $link_on_section == 'y') {
		$smarty->assign('link_on_section', 'y');
	} else {
		 $smarty->assign('link_on_section', 'n');
	}
	if (isset($css)) {
		static $idCssmenu;
		if (isset($type) && ($type == 'vert' || $type == 'horiz')) {
			$css = "cssmenu_$type.css";
		} else {
			$css = 'cssmenu.css';
			$type = '';
		}
		$headerlib->add_cssfile("css/$css", 50);
		$headerlib->add_jsfile('lib/menubuilder/menu.js');
		$tpl = 'tiki-user_cssmenu.tpl';
		$smarty->assign('menu_type', $type);
		$smarty->assign('idCssmenu', $idCssmenu++);
	} else {
		$tpl = 'tiki-user_menu.tpl';
	}
	$menu_info = $tikilib->get_menu($id);
	$channels = $tikilib->list_menu_options($id,0,-1,'position_asc','','',isset($prefs['mylevel'])?$prefs['mylevel']:0);
	$channels = $menulib->setSelected($channels, isset($sectionLevel)?$sectionLevel:'');
	$channels = $tikilib->sort_menu_options($channels);
	$smarty->assign('menu_channels',$channels['data']);
	$smarty->assign('menu_info',$menu_info);
    $smarty->display($tpl);
}

function compare_menu_options($a, $b) { return strcmp(tra($a['name']), tra($b['name'])); }

?>
