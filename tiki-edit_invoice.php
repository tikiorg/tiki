<?php
require_once('tiki-setup.php');
require_once('lib/profilelib/installlib.php');
require_once('lib/profilelib/profilelib.php');
require_once('lib/trackers/trackerlib.php');
require_once('lib/trackers/trackerquerylib.php');

global $tikilib, $trklib, $trkqrylib;

$access->check_feature('feature_invoice');
$access->check_permission('tiki_p_admin');
print_r($trkqrylib->tracker_query_by_names("Invoice Items"));

//check if profile is created
$installer = new Tiki_Profile_Installer();
$profile = Tiki_Profile::fromNames( "profiles.tiki.org", "Invoice" );
if (!$installer->isInstalled( $profile )) {
	$smarty->assign('msg', tra('You need to apply the "Invoice" profile'));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign("clients", $trkqrylib->tracker_query_by_names("Invoice Clients"));
$smarty->assign("setting", end($trkqrylib->tracker_query_by_names("Invoice Settings")));
print_r($trkqrylib->tracker_query_by_names("Invoices"));
$invoiceItems = array();

//we are editing an invoice here
if (isset($_REQUEST['invoice'])) {
	$smarty->assign("invoice", end($trkqrylib->tracker_query_by_names("Invoices", null, null, $_REQUEST['invoice'])));
	
	$invoiceItems = $trkqrylib->tracker_query_by_names("Invoice Items", null, null, null, array($_REQUEST['invoice']), null, array("Invoice Id"));
} else {
	$_REQUEST['invoice'] = 0;
}

(int)$_REQUEST['invoice'] = $_REQUEST['invoice'];

//we add an extra item to the end of invoiceItems, so we can duplicate it on the page
$invoiceItems[] = array(
	"Quantity" => "",
	"Work Description" => "",
	"Taxable" => "",
	"Amount" => "",
);
$smarty->assign("invoiceItems", $invoiceItems);

//we are updating or adding
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
	$_REQUEST['invoice'] = processItem("Invoices", array(
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
	), $_REQUEST, $_REQUEST['invoice']);
	//end invoice
	
	//start invoice items
	$invoiceItems = array();
	
	for($i = 0; $i < count($_REQUEST["Amount"]); $i++) {
		$invoiceItem = processItem("Invoice Items", array(
			"Invoice Id",
			"Amount",
			"Quantity",
			"Work Description",
			"Taxable",
		), $_REQUEST, $_REQUEST['InvoiceItemId'][$i], $i);
		
		array_push($invoiceItems, $invoiceItem);
	}
	
	print_r(array($_REQUEST['invoice'] => $invoiceItems));
	//end invoice items
}

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
			InvoiceItemId.val(InvoiceItemId.val() ? InvoiceItemId.val() : 0);
		});
	});
");

// Display the template
$smarty->assign('mid', 'tiki-edit_invoice.tpl');
$smarty->display("tiki.tpl");