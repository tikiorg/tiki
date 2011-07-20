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
$profile = Tiki_Profile::fromNames( "profiles.tiki.org","Invoice" );
if (!$installer->isInstalled( $profile )) {
	$smarty->assign('msg', tra('You need to apply the "Invoice" profile'));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign("clients", $trkqrylib->tracker_query_by_names("Invoice Clients"));
$smarty->assign("setting", end($trkqrylib->tracker_query_by_names("Invoice Settings")));
$invoiceItems = array();

//we are editing an invoice here
if (isset($_REQUEST['invoice'])) {
	$smarty->assign("invoice", end($trkqrylib->tracker_query_by_names("Invoices", null, null, $_REQUEST['invoice'])));
	
	$invoiceItems = $trkqrylib->tracker_query_by_names("Invoice Items", null, null, null, array($_REQUEST['invoice']), null, array("Invoice Id"));
}

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
	//testing post back
	print_r($_REQUEST);
	die;
	$invoicesFieldIds = $trklib->get_fields_by_names("Invoices", array(
		"Invoice Id",
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
		"Client Name",
		"Item Amounts",
		"Item Quantities",
		"Amounts Paid",
	));

	$invoiceItemsFieldIds = $trklib->get_fields_by_names("Invoice Items", array(
		"Invoice Item Id",
		"Invoice Id",
		"Amount",
		"Quantity",
		"Work Description",
		"Taxable",
	));
	
	//This part doesn't yet work, to update or save invoice
	
	if (isset($_REQUEST['invoice'])) { //edit
		
	} else { //new
		$newInvoice = array();
		
		foreach($invoicesFieldIds as $invoicesFieldId) {
			$newInvoice[] = array(
				'fieldId' => $invoicesFieldId,
				'value' => 99999
			);
		}
		
		$_REQUEST['invoice'] = $trklib->replace_item($trklib->get_tracker_by_name("Invoices"), 0, array('data' => $newInvoice));
	}
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
		
		newInvoiceItem.find(':input').val('');
		
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
");

// Display the template
$smarty->assign('mid', 'tiki-edit_invoice.tpl');
$smarty->display("tiki.tpl");