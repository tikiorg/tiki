<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('tiki-setup.php');
$trklib = TikiLib::lib('trk');

$access->check_feature('feature_invoice');
$access->check_permission('tiki_p_admin');

//check if profile is created
if ($trklib->get_tracker_by_name("Invoice Items") < 1) {
	$smarty->assign('msg', tra('You need to apply the "Invoice" profile'));
	$smarty->display("error.tpl");
	die;
}

$Invoices = Tracker_Query::tracker("Invoices")
	->byName(true)
	->query();

foreach ($Invoices as $I => $Invoice) {
	$Amount = 0;
	$Paid = 0;
	$Status = "";
	
	if (is_array($Invoice["Item Amounts"])) {
		foreach ($Invoice["Item Amounts"] as $Key => $sum) {
			$Amount += $Invoice["Item Amounts"][$Key] * $Invoice["Item Quantities"][$Key];
		}
	} else {
		$Amount = $Invoice["Item Amounts"] * $Invoice["Item Quantities"];
	}
	
	$Invoices[$I]["Amount"] = $Amount;
	
	if (is_array($Invoice["Amounts Paid"])) {
		foreach ($Invoice["Amounts Paid"] as $Sum) {
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
$smarty->assign("Settings", Tracker_Query::tracker("Invoice Settings")->byName()->query());
$smarty->assign("Amount", $Amount);
$smarty->assign("Paid", $Paid);
$smarty->assign("Status", $Status);

// Display the template
$smarty->assign('mid', 'tiki-list_invoices.tpl');
$smarty->display("tiki.tpl");
