<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (!function_exists('mod_users_list_help')) {
	function mod_users_list_help() {
		return "login=n,realName=n,email=n,lastLogin=n,groups=n,avatar=n,userPage=n,log=n";
	}
}
global $userlib, $feature_wiki_userpage_prefix, $feature_wiki_userpage;
$users = $userlib->get_users(0, -1, 'login_asc', '', '', true);
for ($i = 0; $i < $users['cant']; ++$i) {
	$user = $users['data'][$i]['user'];
	$users['data'][$i]['realName'] = $tikilib->get_user_preference($user,'realName','');
	$users['data'][$i]['avatar'] = $tikilib->get_user_avatar($user);
	if ($feature_wiki_userpage == 'y' && $tikilib->page_exists($feature_wiki_userpage_prefix.$user))
		$users['data'][$i]['userPage'] = $feature_wiki_userpage_prefix.$user;
}
$smarty->assign_by_ref('users', $users['data']);
if (!isset($module_params['login']) || $module_params['login'] != 'n')
	$smarty->assign('show_login', 'y');
if (!isset($module_params['realName']) || $module_params['realName'] != 'n')
	$smarty->assign('show_realName', 'y');
if (!isset($module_params['email']) || $module_params['email'] != 'n')
	$smarty->assign('show_email', 'y');
if (!isset($module_params['lastLogin']) || $module_params['lastLogin'] != 'n')
	$smarty->assign('show_lastLogin', 'y');
if (!isset($module_params['groups']) || $module_params['groups'] != 'n')
	$smarty->assign('show_groups', 'y');
if (!isset($module_params['avatar']) || $module_params['avatar'] != 'n')
	$smarty->assign('show_avatar', 'y');
if (!isset($module_params['userPage']) || $module_params['userPage'] != 'n')
	$smarty->assign('show_userPage', 'y');
if (!isset($module_params['log']) || $module_params['log'] != 'n')
	$smarty->assign('show_log', 'y');
?>