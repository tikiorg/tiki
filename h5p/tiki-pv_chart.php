<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_feature('feature_stats');
$access->check_permission('tiki_p_view_stats');
require_once ("lib/graph-engine/gd.php");
require_once ("lib/graph-engine/graph.multiline.php");

//Define the object
$renderer = new GD_GRenderer(450, 300);
$graph = new MultilineGraphic;
$graph->setTitle(tra('Pageviews'));
//Set some data
if (!isset($_REQUEST["days"])) $_REQUEST["days"] = 7;

$statslib = TikiLib::lib('stats');
$data = $statslib->get_pv_chart_data($_REQUEST["days"]);

foreach ($data['xdata'] as $key => $date) {
	 $data['xdata'][$key] = strtotime($date) / 24 / 3600;
}
$graph->setData(array('x' => $data['xdata'], 'y0' => $data['ydata']));
$graph->setParam('grid-independant-major-font', false);
$graph->setParam('grid-independant-major-guide', false);
$graph->draw($renderer);
$renderer->httpOutput('stats.png');
