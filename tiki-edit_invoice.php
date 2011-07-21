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
	/*This is what an array will look like on post back:
	Array
	(
		[ClientId] => 6336
		[InvoiceNumber] => 123
		[DateIssued] => 4/4/2012
		[Quantity] => Array
			(
				[0] => 3
				[1] => 30
			)
	 
		[WorkDescription] => Array
			(
				[0] => Hourly Rate
				[1] => Hourly Rate 2
			)
	 
		[Taxable] => Array
			(
				[0] => n
				[1] => y
			)
	 
		[Amount] => Array
			(
				[0] => 100
				[1] => 100
			)
	 
		[InvoiceNote] => Invoice Note
		[submit] => Save Invoice
		[invoice] => 0
	)
	*/
	//This part doesn't yet work, to update or save invoice
	
	function processItem($trackerName, $fieldNames, $idFieldName, $i) {
		global $trklib;
		$fieldIds = $trklib->get_fields_by_names($trackerName, $fieldNames);
		$fieldData = array();
		
		foreach($fieldNames as $fieldName) {
			$fieldVal = (isset($i) ? $_REQUEST[str_replace(" ", "", $fieldName)][$i] : $_REQUEST[str_replace(" ", "", $fieldName)]);
			array_push($fieldData, formToTrackerField($fieldIds[$fieldName], $fieldVal));
		}
		
		return $fieldData;
	}
	
	function formToTrackerField($id, $val) {
		return array(
			'fieldId' => $id,
			'value' => $val
		);
	}
	
	$invoice = processItem("Invoices", array(
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
		"Amounts Paid",
	));
	
	$invoiceItems = array();
	
	for($i = 0; $i < count($_REQUEST["Amount"]); $i++) {
		array_push($invoiceItems, processItem("Invoice Items", array(
			"Invoice Id",
			"Amount",
			"Quantity",
			"Work Description",
			"Taxable",
		), $i));
	}
	
	
	print_r($invoiceItems);
	die;
	if (isset($_REQUEST['invoice'])) { //edit
		
	} else { //new
		$newInvoice = array();
		
		foreach($invoicesFieldIds as $invoicesFieldId) {
			$newInvoice[] = array(
				'fieldId' => $invoicesFieldId,
				'value' => 99999
			);
		}
		
		$_REQUEST['invoice'] = $trklib->replace_item($trklib->get_tracker_by_name("Invoices"), $_REQUEST['invoice'], array('data' => $newInvoice));
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
	});
");

// Display the template
$smarty->assign('mid', 'tiki-edit_invoice.tpl');
$smarty->display("tiki.tpl");