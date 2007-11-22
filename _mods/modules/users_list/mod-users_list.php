<?php
// CVS: $Id: mod-users_list.php,v 1.3 2007-11-22 16:26:01 sylvieg Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (!function_exists('mod_users_list_help')) {
	function mod_users_list_help() {
		return "login=y|n,realName=y|n,email=y|n,lastLogin=y|n,groups=y|n,avatar=y|n,userPage=y|n,log=y|n,group=name,includedGroups=y|n,initial=letter";
	}
}
global $userlib, $tikilib, $prefs;
if (isset($module_params['group'])) {
	$group = array($module_params['group']);
	if (isset($module_params['includedGroups']) && $module_params['includedGroups'] == 'y') {
		$group = array_merge($group, $userlib->get_including_groups($group[0]));
	}
 } else {
	$group = '';
 }
$users = $userlib->get_users(0, -1, 'login_asc', '',!empty($module_params['initial'])? $module_params['initial']:'', isset($module_params['groups'])?true: false, $group);
for ($i = 0; $i < $users['cant']; ++$i) {
	$my_user = $users['data'][$i]['user'];
	if (isset($module_params['realName']) && $module_params['realName'] == 'y') {
		$users['data'][$i]['realName'] = $tikilib->get_user_preference($my_user,'realName','');
	}
	if (isset($module_params['avatar']) && $module_params['avatar'] == 'y') {
		$users['data'][$i]['avatar'] = $tikilib->get_user_avatar($my_user);
	}
	if ((isset($module_params['realName']) && $module_params['realName'] == 'y')
		|| (isset($module_params['login']) && $module_params['login'] == 'y')) {
		$users['data'][$i]['info_public'] = $tikilib->get_user_preference($my_user, 'user_information', 'public')!= 'private'?'y':'n';
	}
	if (isset($module_params['userPage']) && $module_params['userPage'] == 'y') {
		global $feature_wiki_userpage;
		if ($prefs['feature_wiki_userpage'] == 'y' or $feature_wiki_userpage == 'y') {
			if (!isset($prefs['feature_wiki_userpage_prefix'])) {//trick compat 1.9, 1.10
				global $feature_wiki_userpage_prefix;
				$pre = $feature_wiki_userpage_prefix;
			} else {
				$pre = $prefs['feature_wiki_userpage_prefix'];
			}
			if ($tikilib->page_exists($pre.$my_user)) {
				$users['data'][$i]['userPage'] = $pre.$my_user;
			}
		}
	}
}
if (isset($module_params['log']) && $module_params['log'] == 'y' && $prefs['feature_actionlog'] != 'y') {
	$module_params['log'] = 'n';
 }
$smarty->assign_by_ref('users', $users['data']);
$smarty->assign_by_ref('module_params_users_list', $module_params);
?>