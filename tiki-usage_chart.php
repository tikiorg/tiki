<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-usage_chart.php,v 1.5 2004-09-08 19:51:51 mose Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//Include the code
require_once ("lib/graph2/graph.php");


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
$graph->parameter['title'] = tra('Usage');
$graph->parameter['x_label'] = tra('Feature');
$graph->parameter['y_label_left'] = tra('Klicks');

$data = $tikilib->get_usage_chart_data();
$graph->x_data=$data['xdata'];
$graph->y_data['usage']=$data['ydata'];
$graph->parameter['point_size'] = 6;
$graph->y_format['usage'] =array('colour' => 'blue', 'bar' => 'fill', 'shadow_offset' => 3);
$graph->y_order = array('usage');
$graph->parameter['y_resolution_left']= 1;
$graph->parameter['y_decimal_left']= 0;
/*
$graph->init();
echo "<pre>";
print_r($graph);
echo "</pre>";
die();
*/
$graph->draw();



?>
