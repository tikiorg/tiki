<?php
require_once('tiki-setup.php');
require_once('lib/profilelib/installlib.php');
require_once('lib/profilelib/profilelib.php');
require_once('lib/trackers/trackerlib.php');
require_once('lib/trackers/trackerquerylib.php');

global $tikilib, $trklib, $trkqrylib;

$access->check_feature('feature_invoice');
$access->check_permission('tiki_p_admin');

//check if profile is created
$installer = new Tiki_Profile_Installer();
$profile = Tiki_Profile::fromNames( "profiles.tiki.org", "Invoice" );
if (!$installer->isInstalled( $profile )) {
	$smarty->assign('msg', tra('You need to apply the "Invoice" profile'));
	$smarty->display("error.tpl");
	die;
}

(int)$_REQUEST['InvoiceId'] = $_REQUEST['InvoiceId'];

//handle saving data (edit or update)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	//form to tracker item transformation
	function processItem($trackerName, $fieldNames, $fieldValues, $itemId, $i) {
		global $trklib;
		
		$fields = $trklib->list_tracker_fields($trklib->get_tracker_by_name($trackerName));
		foreach($fields['data'] as $key => $field) {
			$fieldName = $field['name'];	
			$fieldValue = (isset($i) ? $fieldValues[str_replace(" ", "", $fieldName)][$i] : $fieldValues[str_replace(" ", "", $fieldName)]);
			$fields['data'][$key]['value'] = (empty($fieldValue) ? '' : $fieldValue);
		}
		
		return $trklib->replace_item($trklib->get_tracker_by_name($trackerName), $itemId, $fields, 'o');
	}
	
	//start invoice
	$_REQUEST['InvoiceId'] = processItem("Invoices", array(
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
	), $_REQUEST, $_REQUEST['InvoiceId']);
	//end invoice
	
	//start invoice items
	$invoiceItems = array();
	
	$_TEMP = $_REQUEST;
	$itemsToDelete = array();
	foreach(explode(',', $_REQUEST['InvoiceItemIds']) as $itemId) {
		$itemsToDelete[$itemId] = $itemId;
	}
	
	$_TEMP['InvoiceId'] = array();
	for($i = 0; $i < count($_REQUEST['InvoiceItemId']); $i++) {
		$_TEMP['InvoiceId'][$i] = $_REQUEST['InvoiceId'];
		
		$invoiceItem = processItem("Invoice Items", array(
			"Invoice Id",
			"Amount",
			"Quantity",
			"Work Description",
			"Taxable",
		), $_TEMP, $_REQUEST['InvoiceItemId'][$i], $i);
		
		if (isset($itemsToDelete[$_REQUEST['InvoiceItemId'][$i]])) {
			unset($itemsToDelete[$_REQUEST['InvoiceItemId'][$i]]);
		}
		
		array_push($invoiceItems, $invoiceItem);
	}
	//end invoice items

	//here I need to delete items that were deleted on the page
	//:)
	//end delete
	
	header( 'Location: tiki-view_invoice.php?InvoiceId='.$_REQUEST['InvoiceId'] ) ;
	die;
}

$invoiceItems = array();
if (!empty($_REQUEST['InvoiceId'])) {
	$invoice = end($trkqrylib->tracker_query_by_names("Invoices", null, null, $_REQUEST['InvoiceId']));
	$invoice['Item Ids'] = implode(',', $invoice['Item Ids']);
	$smarty->assign("invoice", $invoice);
	
	$invoiceItems = $trkqrylib->tracker_query_by_names("Invoice Items", null, null, null, array($_REQUEST['InvoiceId']), null, array("Invoice Id"));
} else {
	$_REQUEST['InvoiceId'] = 0;
}

$smarty->assign("InvoiceId", $_REQUEST['InvoiceId']);
$smarty->assign("clients", $trkqrylib->tracker_query_by_names("Invoice Clients"));
$smarty->assign("setting", end($trkqrylib->tracker_query_by_names("Invoice Settings")));

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

$headerlib->add_jq_onready("
	function setupTotal() {
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
					.attr('checked', 'true');
			}
		});
		
		$('.InvoiceItemId').each(function() {
			var InvoiceItemId = $(this);
			InvoiceItemId.val(InvoiceItemId.val() ? InvoiceItemId.val() : '0');
		});
		
		var InvoiceId = $('#InvoiceId');
		InvoiceId.val(InvoiceId.val() ? InvoiceId.val() : 0);
	});
");

// Display the template
$smarty->assign('mid', 'tiki-edit_invoice.tpl');
$smarty->display("tiki.tpl");