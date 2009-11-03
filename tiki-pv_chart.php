<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-pv_chart.php,v 1.13 2007-10-12 07:55:29 nyloth Exp $
//Include the code
require_once ('tiki-setup.php');
if ($prefs['feature_stats'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_stats");
	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_view_stats != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied. You cannot view this section"));
	$smarty->display("error.tpl");
	die;
}
require_once ("graph-engine/graph.multiline.php");
require_once ("graph-engine/gd.php");
//Define the object
$renderer = & new GD_GRenderer(450, 300);
$graph = & new MultilineGraphic;
$graph->setTitle(tra('Pageviews'));
//Set some data
if (!isset($_REQUEST["days"])) $_REQUEST["days"] = 7;
$data = $tikilib->get_pv_chart_data($_REQUEST["days"]);
foreach($data['xdata'] as $key => $date) $data['xdata'][$key] = strtotime($date) / 24 / 3600;
$graph->setData(array('x' => $data['xdata'], 'y0' => $data['ydata']));
$graph->setParam('grid-independant-major-font', false);
$graph->setParam('grid-independant-major-guide', false);
$graph->draw($renderer);
$renderer->httpOutput('stats.png');
