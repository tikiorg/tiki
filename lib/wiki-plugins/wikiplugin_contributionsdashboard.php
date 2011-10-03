<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_contributionsdashboard_info() {
	return array(
		'name' => tra('Contributions Dashboard'),
		'documentation' => '',
		'description' => tra('List users contributions to your work'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_contributionsdashboard' ),
		'tags' => array( 'basic' ),		
		'body' => tra('Notice'),
		'format' => 'html',
		'icon' => 'pics/icons/database_table.png',
		'filter' => 'text',
		'params' => array(
			'start' => array(
				'required' => false,
				'name' => tra('Start Date'),
				'description' => tra('Default Beginning Date'),
				'filter' => 'striptags',
				'default' => 'Today - 7 days',
			),
			'end' => array(
				'required' => false,
				'name' => tra('End Date'),
				'description' => tra('Default Ending Date'),
				'filter' => 'striptags',
				'default' => 'Today',
			),
		),
	);
}

function wikiplugin_contributionsdashboard($data, $params) {
	global $tikilib, $headerlib;
	$trklib = TikiLib::lib("trk");
	$trkqrylib = TikiLib::lib("trkqry");
	$logsqrylib = TikiLib::lib("logsqry");
	$smarty = TikiLib::lib("smarty");
	
	static $iContributionsDashboard = 0;
	++$iContributionsDashboard;
	$i = $iContributionsDashboard;
	
	$smarty->assign('iContributionsDashboard', $iContributionsDashboard);
	
	$default = array(
		"start"=> time() - (7 * 24 * 60 * 60),
		"end"=> time()
	);
	
	$params = array_merge($default, $params);
	
	extract($params,EXTR_SKIP);
	
	$headerlib->add_jsfile("lib/jquery.sheet/plugins/raphael-min.js", "external");
	$headerlib->add_jsfile("lib/jquery.sheet/plugins/g.raphael-min.js", "external");
	
	$raphaelData = array();
	$raphaelDates = array();
	
	foreach(LogsQueryLib::wikiPage($_REQUEST['page'])
			->viewed()
			->desc()
			->start($start)
			->end($end)
			->countByDate() as $log) {
		
		$raphaelData[] = $log['count'] * 1;
		$raphaelDates[] = $log['date'];
	}
	
	$headerlib->add_jq_onready("
		var raphael$i = Raphael($('#raphael$i')[0]);
		
		raphael$i.data = ".json_encode($raphaelData).";
		raphael$i.dates = ".json_encode($raphaelDates).";
		
		raphael$i.g.barchart(10,10, $('#raphael$i').width(),$('#raphael$i').height(), [raphael$i.data])
			.hover(function () {
				this.flag = raphael$i.g.popup(
					this.bar.x,
					this.bar.y,
					raphael1.dates[this.bar.id] + '- ' + this.bar.value || '0'
				).insertBefore(this);
			},function () {
				this.flag.animate({
					opacity: 0
				},
				300,
				function () {
					this.remove();
				});
			});
	");
	
	$result = "<div id='raphael$i'></div>";
	
	return $result;
}
