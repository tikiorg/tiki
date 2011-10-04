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
			'types' => array(
				'required' => true,
				'name' => tra('Dashboard Types'),
				'description' => tra('The type of charts that will be rendered seperated by comma'),
				'filter' => 'striptags',
				'default' => 'trackeritems',
			),
		),
	);
}

function wikiplugin_contributionsdashboard($data, $params) {
	global $tikilib, $headerlib, $user;
	$trklib = TikiLib::lib("trk");
	$trkqrylib = TikiLib::lib("trkqry");
	$logsqrylib = TikiLib::lib("logsqry");
	$smarty = TikiLib::lib("smarty");
	
	static $iContributionsDashboard = 0;
	++$iContributionsDashboard;
	$i = $iContributionsDashboard;
	
	$smarty->assign('iContributionsDashboard', $iContributionsDashboard);
	
	$default = array(
		"start"=> 	time() - (7 * 24 * 60 * 60),
		"end"=> 	time(),
		"types"=> 	"trackeritems"
	);
	
	$params = array_merge($default, $params);
	
	extract($params, EXTR_SKIP);
	
	$start = (!empty($_REQUEST["raphaelStart$i"]) ? strtotime($_REQUEST["raphaelStart$i"]) : $start);
	$end = (!empty($_REQUEST["raphaelEnd$i"]) ? strtotime($_REQUEST["raphaelEnd$i"]) : $start);
	
	$types = explode(',', $types);
	
	$headerlib->add_jsfile("lib/jquery.sheet/plugins/raphael-min.js", "external");
	$headerlib->add_jsfile("lib/jquery.sheet/plugins/g.raphael-min.js", "external");
	$headerlib->add_jq_onready("
		$('.cDashDate').datepicker();
	");
	
	foreach($types as $type) {
		if ($type == "trackeritems") {
			$raphaelData = array();
			$raphaelDates = array();
			
			//simon should be replaced with global $user when done
			foreach(LogsQueryLib::trackerItem()->start($start)->end($end)->countTrackerItemsByDate("simon") as $log) {
				$raphaelData[] = $log['count'] * 1;
				$raphaelDates[] = $log['date'];
			}

			$headerlib->add_jq_onready("				
				var r = Raphael($('#raphaelTrackeritems$i')[0]);
				
				var data = ".json_encode($raphaelData).";
				var dates = ".json_encode($raphaelDates).";
				
				r.g.barchart(10,10, $('#raphaelTrackeritems$i').width(),$('#raphaelTrackeritems$i').height(), [data])
					.hover(function () {
						this.flag = r.g.popup(
							this.bar.x,
							this.bar.y,
							dates[this.bar.id] + '- ' + this.bar.value || '0'
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
			
			$result .= "<div id='raphaelTrackeritems$i' style='width: 100%; height: 400px; display: block;'></div>";
		}
	}
	
	return "
			<style>
				.headerHelper {
					font-size: 14px;
					padding-left: 20px;
				}
			</style>
			<div class='ui-widget ui-widget-content ui-corner-all'>
				<h3 class='ui-state-default ui-corner-tl ui-corner-tr' style='margin: 0; padding: 5px;'>
					".tr("Contributions Dashboard")."
					<span class='headerHelper'>
						".tr("Date Range")."
						<form>
							<input type='text' name='raphaelStart$i' id='raphaelStart$i' class='cDashDate' value='".strftime("%m/%d/%Y", $start)."' />
							<input type='text' name='raphaelEnd$i' id='raphaelEnd$i' class='cDashDate' value='".strftime("%m/%d/%Y", $end)."' />
							<input type='hidden' name='refresh' value='1' />
							<input type='submit' id='raphaelUpdate$i' value='".tr("Update")."' />
						</form>
					</span>
				</h3>
				$result
			</div>";
}
