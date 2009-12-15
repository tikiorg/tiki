<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-usage_chart.php,v 1.11 2007-10-12 07:55:32 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//Include the code
require_once ('tiki-setup.php');

require_once ("graph-engine/graph.bar.php");
require_once ("graph-engine/gd.php");
include_once ('lib/stats/statslib.php');

if ($prefs['feature_stats'] != 'y') {
	die;
}

if ($tiki_p_view_stats != 'y') {
	die;
}

//Define the object


if (isset($_REQUEST["type"])) {
	if ($_REQUEST["type"]=="daily") {
		$renderer = &new GD_GRenderer(450,400);
		$graph = &new MultibarGraphic;
		$data = $statslib->get_daily_usage_chart_data();
		$graph->setTitle( tra('Daily Usage') );
		$graph->setData( array( 'x' => $data['xdata'], 'y0' => $data['ydata'] ) );
		$graph->setParam( 'grid-independant-location', 'vertical' );
		$graph->setParam( 'grid-independant-major-font', 'Normal-Text' );
		$graph->setParam( 'grid-independant-major-guide', false );
	}
} else {
	$renderer = &new GD_GRenderer(450,300);
	$graph = &new MultibarGraphic;
	$data = $tikilib->get_usage_chart_data();
	$graph->setTitle( tra('Usage') );
	$graph->setData( array( 'x' => $data['xdata'], 'y0' => $data['ydata'] ) );
	$graph->setParam( 'grid-independant-location', 'vertical' );
	$graph->setParam( 'grid-independant-major-font', 'Normal-Text' );
	$graph->setParam( 'grid-independant-major-guide', false );
}


$graph->draw( $renderer );

$renderer->httpOutput( 'stats.png' );

?>
