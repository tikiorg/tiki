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
if ($_REQUEST["drop"]) {
	$tinvoicelib->drop_bank($_REQUEST["drop"]);
	header("Location: tiki-tinvoice_banks.php");
}
if ($_REQUEST["save"] && $_REQUEST["bankName"]) {
	$data=array();
	$data["name"]=$_REQUEST["bankName"];
	$data["bank"]=$_REQUEST["bank"];
	$data["account_nb"]=$_REQUEST["bankNumber"];
	$data["rib"]=$_REQUEST["bankRib"];
	$data["swift"]=$_REQUEST["bankSwift"];
	$bankId=$tinvoicelib->update_bank($userId,$_REQUEST["bankId"],$data);
	$smarty->assign("bankId",$bankId);
	header("Location: tiki-tinvoice_banks.php");
}
if ($_REQUEST["bankId"]) {
	$bank=$tinvoicelib->get_bank($_REQUEST["bankId"]);
	$smarty->assign("bank",$bank);
}



// Display the template
$smarty->assign('mid', 'tiki-tinvoice_bank_edit.tpl');
$smarty->display("tiki.tpl");

?>
