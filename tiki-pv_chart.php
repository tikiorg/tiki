<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-pv_chart.php,v 1.10 2005-01-01 00:16:34 damosoft Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//Include the code
require_once("lib/graph2/graph.php");


require_once ('tiki-setup.php');

if ($feature_stats != 'y') {
	die;
}

if ($tiki_p_view_stats != 'y') {
	die;
}

//Define the object
$graph = new graph(400,300);
$graph->parameter['path_to_fonts'] = 'lib/graph2/';
$graph->parameter['title'] = tra('Pageviews');
$graph->parameter['x_label'] = tra('Day');
$graph->parameter['y_label_left'] = tra('Clicks');


//Set some data
if (!isset($_REQUEST["days"]))
	$_REQUEST["days"] = 7;

$data = $tikilib->get_pv_chart_data($_REQUEST["days"]);
$graph->x_data=$data['xdata'];
$graph->y_data['pageviews']=$data['ydata'];
$graph->parameter['point_size'] = 6;
$graph->y_format['pageviews'] =array('colour' => 'blue', 'line' => 'line', 'point' => 'square-open');
$graph->y_order = array('pageviews');
$graph->parameter['y_resolution_left']= 1;
$graph->parameter['y_decimal_left']= 0;
$graph->init();
/*
echo "<pre>";
print_r($graph);
echo "</pre>";
die();
*/
$graph->draw();

?>
