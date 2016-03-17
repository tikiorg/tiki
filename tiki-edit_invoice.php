<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

//handle saving data (edit or update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//start invoice
	/*
	$_REQUEST['InvoiceId'] = $trklib->replaceItemFromRequestValues(
					$trklib->get_tracker_by_name("Invoices"),
					array(
						"Client Id",
						"Invoice Number",
						"Date Issued",
						"Payment Term",
						"Tax 1 Description",
						"Tax 1 Rate",
						"Tax 2 Description",
						"Tax 2 Rate",
						"Invoice Note",
						"Days Payment Due",
					),
					$_REQUEST, $_REQUEST['InvoiceId']
	);*/
	die;
	//end invoice

	//start invoice items
	$invoiceItems = array();

	$_TEMP = $_REQUEST;
	$itemsToDelete = array();
	foreach (explode(',', $_REQUEST['InvoiceItemIds']) as $itemId) {
		$itemsToDelete[$itemId] = $itemId;
	}

	$_TEMP['InvoiceId'] = array();
	for ($i = 0, $count_InvoiceItemId = count($_REQUEST['InvoiceItemId']); $i < $count_InvoiceItemId; $i++) {
		$_TEMP['InvoiceId'][$i] = $_REQUEST['InvoiceId'];

		$invoiceItem = $trklib->replaceItemFromRequestValues(
			$trklib->get_tracker_by_name("Invoice Items"),
			array(
				"Invoice Id",
				"Amount",
				"Quantity",
				"Work Description",
				"Taxable",
			),
			$_TEMP,
			$_REQUEST['InvoiceItemId'][$i],
			$i
		);

		if (isset($itemsToDelete[$_REQUEST['InvoiceItemId'][$i]])) {
			unset($itemsToDelete[$_REQUEST['InvoiceItemId'][$i]]);
		}

		array_push($invoiceItems, $invoiceItem);
	}
	//end invoice items

	//here I need to delete items that were deleted on the page
	foreach ($itemsToDelete as $itemId) {
		$trklib->remove_tracker_item($itemId);
	}
	//end delete

	header('Location: tiki-view_invoice.php?InvoiceId='.$_REQUEST['InvoiceId']);
	die;
}

$invoiceItems = array();
if (!empty($_REQUEST['InvoiceId'])) {
	$invoice = Tracker_Query::tracker("Invoices")
		->byName()
		->equals($_REQUEST['InvoiceId'])
		->getOne();

	$invoice['Item Ids'] = implode(',', $invoice['Item Ids']);
	$smarty->assign("invoice", $invoice);

	$invoiceItems = Tracker_Query::tracker("Invoice Items")
		->byName()
		->fields(array("Invoice Id"))
		->search(array($_REQUEST['InvoiceId']))
		->query();
} else {
	$_REQUEST['InvoiceId'] = 0;
}

//give the user the last invoice number
$LastInvoice = Tracker_Query::tracker("Invoices")
	->byName()
	->limit(0)
	->offset(1)
	->desc(true)
	->excludeDetails()
	->getOne();

$NewInvoiceNumber = (isset($LastInvoice["Invoice Number"]) ? $LastInvoice["Invoice Number"] + 1 : 1);
$smarty->assign("NewInvoiceNumber", $NewInvoiceNumber);

$smarty->assign("InvoiceId", $_REQUEST['InvoiceId']);
$smarty->assign("clients", Tracker_Query::tracker("Invoice Clients")->byName()->query());
$smarty->assign("setting", Tracker_Query::tracker("Invoice Settings")->byName()->getOne());

//we add an extra item to the end of invoiceItems, so we can duplicate it on the page
if (count($invoiceItems) < 1) {
	$invoiceItems[] = array(
		"Quantity" => "",
		"Work Description" => "",
		"Taxable" => "",
		"Amount" => "",
	);
}
$smarty->assign("invoiceItems", $invoiceItems);

$headerlib->add_jq_onready(
    "function setupTotal() {
		$('#InvoiceForm :input')
			.unbind('change')
			.change(function() {
				findTotal();
			})
			.change();
	}

	function findTotal() {
		var total = 0;
		$('.InvoiceItem').each(function() {
			var itemTotal = $(this).find('.InvoiceQuantity').val() * $(this).find('.InvoiceAmount').val();
			total += itemTotal;
		});
		$('#Amount').text(total);
	}

	setupTotal();

	$('#InvoiceNewItem').click(function() {
		var lastInvoiceItem = $('.InvoiceItem:last');
		var newInvoiceItem = lastInvoiceItem.clone();

		newInvoiceItem.find(':input').not(':checkbox,:button').val('');

		newInvoiceItem.insertAfter(lastInvoiceItem);

		setupTotal();
	});

	$('#InvoiceForm').click(function(e) {
		if ($(e.target).hasClass('DeleteItem')) {
			if ($('.InvoiceItem').length > 1) {
				$(e.target).parent().parent().remove();
			}
			return false;
		}
	});

	$('#InvoiceForm').submit(function() {
		$('.InvoiceTaxable').each(function() {
			var InvoiceTaxable = $(this);
			if (!InvoiceTaxable.is(':checked')) {
				InvoiceTaxable
					.val('n')
					.prop('checked', 'true');
			}
		});

		$('.InvoiceItemId').each(function() {
			var InvoiceItemId = $(this);
			InvoiceItemId.val(InvoiceItemId.val() ? InvoiceItemId.val() : '0');
		});

		var InvoiceId = $('#InvoiceId');
		InvoiceId.val(InvoiceId.val() ? InvoiceId.val() : 0);

		return false;
	});"
);

// Display the template
$smarty->assign('mid', 'tiki-edit_invoice.tpl');
$smarty->display("tiki.tpl");
