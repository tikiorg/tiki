<?php
require_once('tiki-setup.php');
$trklib = TikiLib::lib('trk');
$trkqrylib = TikiLib::lib('trkqry');

$access->check_feature('feature_invoice');
$access->check_permission('tiki_p_admin');

//check if profile is created
if ($trklib->get_tracker_by_name("Invoice Items") < 1) {
	$smarty->assign('msg', tra('You need to apply the "Invoice" profile'));
	$smarty->display("error.tpl");
	die;
}

(int)$_REQUEST['InvoiceId'] = $_REQUEST['InvoiceId'];
$smarty->assign('InvoiceId', $_REQUEST['InvoiceId']);
$invoice = end($trkqrylib->tracker_query_by_names("Invoices", null, null, $_REQUEST['InvoiceId']));
$amount = 0;

if (is_array($invoice["Item Amounts"])) {
	foreach($invoice["Item Amounts"] as $key => $sum) {
		$amount += $invoice["Item Amounts"][$key] * $invoice["Item Quantities"][$key];
	}
} else {
	$amount = $invoice["Item Amounts"] * $invoice["Item Quantities"];
}

$smarty->assign("invoice", $invoice);
$smarty->assign("amount", $amount);
$smarty->assign("client", end($trkqrylib->tracker_query_by_names("Invoice Clients", null, null, null, array($invoice['Client Id']), null, array("Client Id"))));
$smarty->assign("setting", end($trkqrylib->tracker_query_by_names("Invoice Settings")));
$smarty->assign("invoiceItems", $trkqrylib->tracker_query_by_names("Invoice Items", null, null, null, array($_REQUEST['InvoiceId']), null, array("Invoice Id")));

// Display the template
$smarty->assign('mid', 'tiki-view_invoice.tpl');
$smarty->display("tiki.tpl");