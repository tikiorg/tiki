<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-usage_chart.php,v 1.2 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//Include the code
include ("lib/phplot.php");

require_once ('tiki-setup.php');

if ($feature_stats != 'y') {
	die;
}

if ($tiki_p_view_stats != 'y') {
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