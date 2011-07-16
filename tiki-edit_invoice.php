<?php
require_once('tiki-setup.php');
include('lib/trackers/trackerquerylib.php');

global $tikilib, $trkqrylib, $smarty;

$access->check_permission('tiki_p_admin');

$smarty->assign("invoice", end($trkqrylib->tracker_query_by_names("Invoices", null, null, $_REQUEST['invoice'])));
$smarty->assign("clients", $trkqrylib->tracker_query_by_names("Invoice Clients"));
$smarty->assign("setting", end($trkqrylib->tracker_query_by_names("Invoice Settings")));
$smarty->assign("invoiceItems", $trkqrylib->tracker_query_by_names("Invoice Items", null, null, null, array($_REQUEST['invoice']), null, array("Invoice Id")));

// Display the template
$smarty->assign('mid', 'tiki-edit_invoice.tpl');
$smarty->display("tiki.tpl");