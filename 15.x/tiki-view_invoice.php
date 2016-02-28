<?php
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

(int)$_REQUEST['InvoiceId'] = $_REQUEST['InvoiceId'];
$smarty->assign('InvoiceId', $_REQUEST['InvoiceId']);
$invoice = Tracker_Query::tracker("Invoices")
	->byName()
	->equals($_REQUEST['InvoiceId'])
	->getOne();

$amount = 0;

if (is_array($invoice["Item Amounts"])) {
	foreach ($invoice["Item Amounts"] as $key => $sum) {
		$amount += $invoice["Item Amounts"][$key] * $invoice["Item Quantities"][$key];
	}
} else {
	$amount = $invoice["Item Amounts"] * $invoice["Item Quantities"];
}

$smarty->assign("invoice", $invoice);
$smarty->assign("amount", $amount);
$smarty->assign(
	"client",
	Tracker_Query::tracker("Invoice Clients")
	->fields(array("Client Id"))->equals(array($invoice['Client Id']))
	->byName()
	->getOne()
);
$smarty->assign(
	"setting",
	Tracker_Query::tracker("Invoice Settings")
	->byName()
	->query()
);
$smarty->assign(
	"invoiceItems",
	Tracker_Query::tracker("Invoice Items")
	->fields(array("Invoice Id"))->equals(array($_REQUEST['InvoiceId']))
	->byName()
	->query()
);

// Display the template
$smarty->assign('mid', 'tiki-view_invoice.tpl');
$smarty->display("tiki.tpl");
