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
	$tinvoicelib->drop_transaction($_REQUEST["drop"]);
	header("Location: tiki-tinvoice_transactions.php");
}
if ($_REQUEST["save"] && $_REQUEST["date"]) {
	$data=array();
	$data["bankId"]=$_REQUEST["bankId"];
	$data["date"]=$_REQUEST["date"];
	$data["operation_nb"]=$_REQUEST["operationNb"];
	$data["label"]=$_REQUEST["label"];
	$data["debit"]=$_REQUEST["debit"];
	$data["credit"]=$_REQUEST["credit"];
	$data["status"]=$_REQUEST["status"];
	$tId=$tinvoicelib->update_transaction($userId,$_REQUEST["tId"],$data);
	$smarty->assign("tId",$tId);
	header("Location: tiki-tinvoice_transactions.php");
}
// get banks accounts
$banks=$tinvoicelib->list_banks($userId, $_REQUEST["bankId"]);
$smarty->assign("banks",$banks);
if ($_REQUEST["tId"]) {
	$transaction=$tinvoicelib->get_transaction($_REQUEST["tId"]);
	$smarty->assign("transaction",$transaction);
}

// Display the template
$smarty->assign('mid', 'tiki-tinvoice_transaction_edit.tpl');
$smarty->display("tiki.tpl");

?>
