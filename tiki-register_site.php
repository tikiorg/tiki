<?php
// Initialization
require_once('tiki-setup.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

include_once('lib/directory/dirlib.php');

$tmp1 = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "";
$tmp2 = isset($_SERVER["PHP_SELF"]) ? $_SERVER["PHP_SELF"] : "";

// concat all, remove the // between server and path and then
// remove the name of the script itself:
$url = $tmp1.dirname($tmp2);

$info = Array();
$info["name"] = $prefs['siteTitle'];
$info["description"] = '';
$info["url"] = $url;
$info["country"] = 'None';
$info["isValid"] = 'n';
$smarty->assign_by_ref('info',$info);

$countries = $tikilib->get_flags();
$smarty->assign_by_ref('countries',$countries);

// Display the template
$smarty->assign('mid','tiki-register_site.tpl');
$smarty->display("tiki.tpl");
?>
