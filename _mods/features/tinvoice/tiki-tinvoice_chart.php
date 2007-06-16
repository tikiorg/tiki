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
if ($tiki_p_tinvoice_chart_view != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}
if ($feature_categories == 'y') {
    include_once ('lib/categories/categlib.php');
}

//$owner=$tinvoicelib->get_owner_contact($contactId);

$tinvoicelib=new TinvoiceLib($dbTiki);

$id_emitter=isset($_REQUEST['id_emitter']) ? (int)$_REQUEST['id_emitter'] : NULL;
$id_receiver=isset($_REQUEST['id_receiver']) ? (int)$_REQUEST['id_receiver'] : NULL;

// get Period graph 
$graphPeriod=$_REQUEST['graphPeriod'];
if ($_REQUEST['todate']) {
$todate=$_REQUEST['todate'];
} else {
$todate= date("%U");
}
// process graph 

include ("lib/jpgraph/src/jpgraph.php");
//include ("lib/jpgraph/src/jpgraph_line.php");
include ("lib/jpgraph/src/jpgraph_bar.php");

// datas
$yvalue=array();
$xvalue=array();

//get timestamp dates array for extracted period  
$period=$tinvoicelib->get_period_dates($todate,$graphPeriod);
if ($graphPeriod == "week") {
	// list week period 
	// get week objects array
	$invoices=$tinvoicelib->extract_Invoices($id_emitter, 'tiki', $id_receiver, 'tiki',$todate,$period);
	// populate week array
	for ($i=0; $i<count($period); $i++) {
		$tdate= $period[$i]['date'];
		array_push($xvalue,strftime("%a %d %h",$tdate));
		$vb='';
		foreach($invoices as $invoice) {
			$a_date=$invoice->get_date_as_timestamp();
			if (strftime("%w",$a_date) == $i) {	
					$vb=$invoice->get_sum_amount();
			}
		}
		if (!$vb) {		
			array_push($yvalue,NULL);
		} else {
			
			array_push($yvalue,$vb);
		}

	}

} else if ($graphPeriod == "month") {
	// list monthly period 
	$invoices=$tinvoicelib->extract_Invoices($id_emitter, 'tiki', $id_receiver, 'tiki',$todate,$period);
	// populate month array
	for ($i=0; $i<=count($period)-1; $i++) {
		$tdate= $period[$i]['date'];
		array_push($xvalue,strftime("%d",$tdate));
		$vb="";
		foreach($invoices as $invoice) {
			$a_date=$invoice->get_date_as_timestamp();
			if (strftime("%d",$a_date) == $i+1) {	
				$vb=$invoice->get_sum_amount();
			}
		}
		if (!$vb) {		
			array_push($yvalue,NULL);
		} else {
			
			array_push($yvalue,$vb);
		}
	}
} else if ($graphPeriod == "trimester") {
	// list monthly period 
	// remove this later
	$xtype="day";
	if ($xtype == "day") {	
		$invoices=$tinvoicelib->extract_Invoices($id_emitter, 'tiki', $id_receiver, 'tiki',$todate,$period);
	}
	// populate trimester array
	for ($i=0; $i<=count($period)-1; $i++) {
		$tdate= $period[$i]['date'];
		array_push($xvalue,strftime("%d-%h",$tdate));
		$vb="";
		foreach($invoices as $invoice) {
			$a_date=$invoice->get_date_as_timestamp();
			if ($a_date == $tdate) {	
				$vb=$invoice->get_sum_amount();
			}
		}
		if (!$vb) {		
			array_push($yvalue,NULL);
		} else {
			
			array_push($yvalue,$vb);
		}
		
	}

} else {
	// list all 

	$invoices=$tinvoicelib->list_invoices($id_emitter, 'tiki', $id_receiver, 'tiki');
	foreach($invoices as $invoice) {
	
		$amount=$invoice->get_amount();
		$a_date=$invoice->get_date_as_timestamp();
		array_push($yvalue,$amount);
		array_push($xvalue,strftime("%d-%m-%Y",$a_date));
	}
}

// Create the graph. These two calls are always required
$graph = new Graph(600,330,"auto"); 
$graph->SetScale("textlin");
$bplot = new BarPlot($yvalue);
if ($graphPeriod == "week") {
$bplot->value->Show();
$bplot->value->SetFormat('%01.2f');
$bplot->value->SetFont(FF_FONT1,FS_BOLD);
// Center the values in the bar
$bplot->SetValuePos('bottom');
$bplot->SetShadow("#e6ae5c",10,10);
$bplot->SetFillGradient("#b36b00","#FF9900",GRAD_RIGHT_REFLECTION);
} else if  ($graphPeriod == "month") {
$bplot->SetFillGradient("#b36b00","#FF9900",GRAD_RIGHT_REFLECTION);
$bplot->SetShadow("steelblue",3,3);
} else {
$bplot->SetShadow("steelblue",2,2);
}
$bplot->SetColor("#FF9900");
$bplot->SetLegend("Invoices");
$bplot->SetWidth(1.0);
$graph->Add($bplot);

// Create and add Text stats
$label="Total : ".array_sum($yvalue);
$graph->tabtitle->Set($label);
$graph->tabtitle->SetTabAlign('right'); 
$graph->tabtitle->SetCorner(2);
$graph->tabtitle->SetFillColor('white');
$graph->tabtitle->SetColor('black','white','orange');

$graph->yaxis->title->Set('Incomes');
$graph->yaxis->title->SetColor('black');
$graph->yaxis->title->SetMargin(10,0,0,0);
$graph->xaxis->SetTickLabels($xvalue);
if ($xtype == "day") {
	$graph->xaxis->SetTextLabelInterval(14);
}
$graph->xaxis->SetTitle('Values for','middle'); 
//$graph->xaxis->scale->ticks->SetLabelDateFormat("d.m.y");
//$graph->xaxis->SetLabelAngle(90);
$graph->title->Set($graphPeriod."ly Combined Invoices");
$graph->img->SetMargin(50,10,30,40);
$graph->legend->Pos(0.03,0.1,"right","top");
$graph->legend->SetShadow();
// Add a drop shadow
$graph->SetShadow();

// Display the graph
$graph->Stroke();

?>
