<?php
// Initialization
$section = 'tinvoice';
require_once ('tiki-setup.php');
require_once ('lib/tinvoice/tinvoicelib.php');
require_once ('lib/webmail/contactlib.php');
if ($feature_tinvoice != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_tinvoice");

	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_tinvoice != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

// xajax addon 
require_once("lib/ajax/ajaxlib.php");


if ($feature_categories == 'y') {
    include_once ('lib/categories/categlib.php');
}

//$owner=$tinvoicelib->get_owner_contact($contactId);

$tinvoicelib=new TinvoiceLib($dbTiki);


if (isset($_REQUEST['graphPeriod'])) {
	$graphPeriod=$_REQUEST['graphPeriod'];
} else {
	$graphPeriod="week";
}
if (isset($_REQUEST['todate'])) {
	$todate=$_REQUEST['todate'];
} else {
	$todate=date("U");
}
if (isset($_REQUEST['xtype'])) {
	$xtype=$_REQUEST['xtype'];
} else {
	$smarty->assign("graphPeriod", $graphPeriod);
	$smarty->assign("todate", $todate);
	$smarty->assign("xtype", $xtype);
	$period=$tinvoicelib->get_period_dates($todate,$graphPeriod);
	$next=$todate + count($period)*24*60*60;
	$prev=$todate - count($period)*24*60*60;
	$smarty->assign("prev", $prev);
	$smarty->assign("next", $next);
}

// xajax addon
$ajaxlib->registerTemplate(tiki-tinvoice_graph.tpl);
$ajaxlib->processRequests();
// Display the template
$smarty->assign('mid', 'tiki-tinvoice_graph.tpl');
$smarty->display("tiki.tpl");
?>
