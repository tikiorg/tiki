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

$Invoices = $trkqrylib->tracker_query_by_names("Invoices");

foreach($Invoices as $I => $Invoice) {
	$Amount = 0;
	$Paid = 0;
	$Status = "";
	
	if (is_array($Invoice["Item Amounts"])) {
		foreach($Invoice["Item Amounts"] as $Key => $sum) {
			$Amount += $Invoice["Item Amounts"][$Key] * $Invoice["Item Quantities"][$Key];
		}
	} else {
		$Amount = $Invoice["Item Amounts"] * $Invoice["Item Quantities"];
	}
	
	$Invoices[$I]["Amount"] = $Amount;
	
	if (is_array($Invoice["Amounts Paid"])) {
		foreach($Invoice["Amounts Paid"] as $Sum) {
			$Paid += $Sum;
		}
	} else {
		$Paid = $Invoice["Amounts Paid"];
	}
	
	$Invoices[$I]["Paid"] = $Paid;
	
	if ($Amount == $Paid) {	
		$Status = "Paid";
	} else {
		$Status = "Open";
	}
	
	$Invoices[$I]["Status"] = $Status;
}

$smarty->assign("Invoices", $Invoices);
$smarty->assign("Settings", end($trkqrylib->tracker_query_by_names("Invoice Settings")));
$smarty->assign("Amount", $Amount);
$smarty->assign("Paid", $Paid);
$smarty->assign("Status", $Status);

// Display the template
$smarty->assign('mid', 'tiki-list_invoices.tpl');
$smarty->display("tiki.tpl");