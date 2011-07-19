<?php
require_once('tiki-setup.php');
require_once('lib/profilelib/installlib.php');
require_once('lib/profilelib/profilelib.php');
require_once('lib/trackers/trackerquerylib.php');

global $tikilib, $trkqrylib;

$access->check_feature('feature_invoice');
$access->check_permission('tiki_p_admin');

//check if profile is created
$installer = new Tiki_Profile_Installer();
$profile = Tiki_Profile::fromNames( "profiles.tiki.org","Invoice" );
if (!$installer->isInstalled( $profile )) {
	$smarty->assign('msg', tra('You need to apply the "Invoice" profile'));
	$smarty->display("error.tpl");
	die;
}

$invoices = $trkqrylib->tracker_query_by_names("Invoices");
$clients = $trkqrylib->tracker_query_by_names("Invoices Clients");
$settings = end($trkqrylib->tracker_query_by_names("Invoice Settings"));


foreach($invoices as $key => $invoice) {
	$amount = 0;
	$paid = 0;
	$status = "";
	
	if (is_array($invoice["Item Amounts"])) {
		foreach($invoice["Item Amounts"] as $key => $sum) {
			$amount += $invoice["Item Amounts"][$key] * $invoice["Item Quantities"][$key];
		}
	} else {
		$amount = $invoice["Item Amounts"] * $invoice["Item Quantities"];
	}
	
	$invoice["Amount"] = $amount;
	
	if (is_array($invoice["Amounts Paid"])) {
		foreach($invoice["Amounts Paid"] as $sum) {
			$paid += $sum;
		}
	} else {
		$paid = $invoice["Amounts Paid"];
	}
	
	$invoice["Paid"] = $paid;
	
	if ($amount == $paid) {	
		$status = "Paid";
	} else {
		$status = "Open";
	}
	
	$invoice["Status"] = $status;
}

$smarty->assign("invoices", $invoices);
$smarty->assign("clients", $clients);
$smarty->assign("settings", $settings);
$smarty->assign("amount", $amount);
$smarty->assign("paid", $paid);
$smarty->assign("status", $status);

// Display the template
$smarty->assign('mid', 'tiki-list_invoices.tpl');
$smarty->display("tiki.tpl");