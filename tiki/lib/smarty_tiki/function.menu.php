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

	if (!isset($sectionLevel))
		$smarty->caching = true;
	if (empty($link_on_section) || $link_on_section == 'y') {
		$smarty->assign('link_on_section', 'y');
	} else {
		 $smarty->assign('link_on_section', 'n');
	}
	if ($user) {
		$uid = md5($tikilib->get_user_cache_id($user));
		$cache_id = "menu$id|" . $uid;
	} else {
		$cache_id = "menu$id|";
	}
	if (isset($css)) {
		if (isset($type) && ($type == 'vert' || $type == 'horiz')) {
			$css = "cssmenu_$type.css";
		} else {
			$css = 'cssmenu.css';
			$type = '';
		}
		$headerlib->add_cssfile("css/$css", 50);
		$tpl = 'tiki-user_cssmenu.tpl';
		$smarty->assign('type', $type);
	} else {
		$tpl = 'tiki-user_menu.tpl';
	}
    if (!$smarty->is_cached($tpl, "$cache_id")) {
		$menu_info = $tikilib->get_menu($id);
		$channels = $tikilib->list_menu_options($id,0,-1,'position_asc','','',isset($prefs['mylevel'])?$prefs['mylevel']:0);
		$channels = $menulib->setSelected($channels, isset($sectionLevel)?$sectionLevel:'');
		$channels = $tikilib->sort_menu_options($channels);
		$smarty->assign('channels',$channels['data']);
		$smarty->assign('menu_info',$menu_info);
    }
    $smarty->display($tpl, "$cache_id");
    $smarty->caching = false;
}

function compare_menu_options($a, $b) { return strcmp(tra($a['name']), tra($b['name'])); }

?>
