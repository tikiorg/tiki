<?php
//Include the code
include("lib/phplot.php");
require_once('tiki-setup.php');
if($feature_stats != 'y') {
  die;  
}

if($tiki_p_view_stats != 'y') {
  die;
}


//Define the object
$graph = new PHPlot;
//Set some data
$example_data = $tikilib->get_usage_chart_data();
$graph->SetDataValues($example_data);
$graph->SetPlotType('bars');
//$graph->SetPlotType('lines');
//Draw it
$graph->DrawGraph();
?>