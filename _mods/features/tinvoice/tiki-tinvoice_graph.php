<?php
// Initialization
$section = 'tinvoice';
require_once ('tiki-setup.php');
require_once ('lib/tinvoice/tinvoicelib.php');
// xajax addon 
require_once("lib/ajax/ajaxlib.php");


if ($feature_tinvoice != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_tinvoice");

	$smarty->display("error.tpl");
	die;
}
if ($feature_ajax != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_ajax");

	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_tinvoice != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}


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
	if (isset($_REQUEST['filter'])) {
		$filter=$_REQUEST['filter'];
	} else {
		$filter="Invoices";
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
		$smarty->assign("filter", $filter);
		$period=$tinvoicelib->get_period_dates($todate,$graphPeriod);
		$next=$todate + count($period)*24*60*60;
		$prev=$todate - count($period)*24*60*60;
		$smarty->assign("prev", $prev);
		$smarty->assign("next", $next);
	}
#-------------Functions used by xajaxlib - it can be an include

function tra_ajax($response) {
	$objResponse = new xajaxResponse();
	$objResponse->addAssign("result", "innerHTML", tra($response['str'],$response['lang']));
	return $objResponse;
}

function load_graph($graphPeriod,$todate) {
	global $graphPeriod, $todate;
	$objResponse = new xajaxResponse();
	//$objResponse->clear("divchart","innerHTML");
	$objResponse->replace("divchart", "innerHTML","<img id='chart' border='0' alt='tinvoice graphs' src='tiki-tinvoice_chart.php?graphPeriod=".$graphPeriod."&todate=".$todate."' />");
 	return $objResponse;
	}

#----------------------------------------------------------------

$xajax = new xajax();
# registering the functions - xajax will generate the js code.
$xajax->registerFunction("tra_ajax");
$xajax->registerFunction("loadComponent");
$xajax->registerFunction("load_graph");
$ajaxlib->registerTemplate("tiki-tinvoice_graph.tpl");
//load_graph($graphPeriod,$todate);
$ajaxlib->processRequests();

#assigning the js code to: xajax_js -> this var will be printed in the template file - {$xajax_js}
$smarty->assign("xajax_js",$xajax->getJavascript('','lib/ajax/xajax_js/xajax.js'));


// Display the template
$smarty->assign('mid', 'tiki-tinvoice_graph.tpl');
$smarty->display("tiki.tpl");
?>
