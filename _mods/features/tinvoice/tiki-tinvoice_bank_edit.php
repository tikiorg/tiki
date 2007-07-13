<?php
// Initialization
$section = 'tinvoice';
require_once ('tiki-setup.php');
require_once ('lib/tinvoice/tinvoicelib.php');

if ($feature_tinvoice != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_tinvoice");

	$smarty->display("error.tpl");
	die;
}
if ($feature_ajax != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_ajax");

	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_tinvoice_edit != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}
$tinvoicelib=new TinvoiceLib($dbTiki);
$userId=$tikilib->get_user_id($user);
if ($_REQUEST["save"] && $_REQUEST["name"]) {
	$data=array();
	$data["name"]=$_REQUEST["name"];
	$data["bank"]=$_REQUEST["bank"];
	$data["account_nb"]=$_REQUEST["account_nb"];
	$data["rib"]=$_REQUEST["rib"];
	$data["swift"]=$_REQUEST["swift"];
	$bankId=$tinvoicelib->update_bank($userId,$_REQUEST["bankId"],$data);
	$smarty->assign("bankId",$bankId);
}

var_dump($banks);



// Display the template
$smarty->assign('mid', 'tiki-tinvoice_bank_edit.tpl');
$smarty->display("tiki.tpl");

?>
