<?php
require_once ('tiki-setup.php');
require_once ('lib/cc/cclib.php');

if ($tiki_p_cc_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$cc_cpun = $tikilib->get_preference('cc_cpun','');
$cc_mail = $tikilib->get_preference('cc_mail',$userlib->get_user_email($contact_user));
$cc_ccsp_ref = $tikilib->get_preference('cc_ccsp_ref','dev.openmoney.org');
$providers_refresh = false;

if (isset($_REQUEST['cc_cpun'])) {
	$tikilib->set_preference('cc_ccsp_ref',$_REQUEST['cc_ccsp_ref']);
	$cc_ccsp_ref = $_REQUEST['cc_ccsp_ref'];
	$tikilib->set_preference('cc_cpun',$_REQUEST['cc_cpun']);
	$cc_cpun = $_REQUEST['cc_cpun'];
	$tikilib->set_preference('cc_mail',$_REQUEST['cc_mail']);
	$cc_mail = $_REQUEST['cc_mail'];
}
if (isset($_REQUEST['providers_refresh'])) {
	$providers_refresh = true;
}

$smarty->assign('cc_cpun',$cc_cpun);
$smarty->assign('cc_mail',$cc_mail);
$smarty->assign('cc_ccsp_ref',$cc_ccsp_ref);

$providers = $cclib->list_providers($cc_ccsp_ref,$providers_refresh);
$smarty->assign('providers',$providers);

$smarty->assign('msg',$cclib->msg);

$smarty->assign('mid', 'cc/admin.tpl');
$smarty->display('tiki.tpl');

?>
