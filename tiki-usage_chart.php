<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature('feature_stats', '', 'general');
$access->check_permission('tiki_p_view_stats');

require_once ('lib/graph-engine/gd.php');
require_once ('lib/graph-engine/graph.bar.php');
$statslib = TikiLib::lib('stats');

//Define the object
if (isset($_REQUEST["type"])) {
	if ($_REQUEST["type"] == "daily") {
		$renderer = new GD_GRenderer(450, 400);
		$graph = new MultibarGraphic;
		$data = $statslib->get_daily_usage_chart_data();
		$graph->setTitle(tra('Daily Usage'));
		$graph->setData(array('x' => $data['xdata'], 'y0' => $data['ydata']));
		$graph->setParam('grid-independant-location', 'vertical');
		$graph->setParam('grid-independant-major-font', 'Normal-Text');
		$graph->setParam('grid-independant-major-guide', false);
	}
} else {
	$renderer = new GD_GRenderer(450, 300);
	$graph = new MultibarGraphic;
	$data = $tikilib->get_usage_chart_data();
	$graph->setTitle(tra('Usage'));
	$graph->setData(array('x' => $data['xdata'], 'y0' => $data['ydata']));
	$graph->setParam('grid-independant-location', 'vertical');
	$graph->setParam('grid-independant-major-font', 'Normal-Text');
	$graph->setParam('grid-independant-major-guide', false);
}
$graph->draw($renderer);
$renderer->httpOutput('stats.png');
