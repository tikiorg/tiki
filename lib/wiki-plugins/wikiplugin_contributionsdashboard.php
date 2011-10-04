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
		"start"=> 	time() - (365 * 24 * 60 * 60),
		"end"=> 	time(),
		"types"=> 	"trackeritems,toptrackeritemsusers"
	);
	
	$params = array_merge($default, $params);
	
	extract($params, EXTR_SKIP);
	
	$start = (!empty($_REQUEST["raphaelStart$i"]) ? strtotime($_REQUEST["raphaelStart$i"]) : $start);
	$end = (!empty($_REQUEST["raphaelEnd$i"]) ? strtotime($_REQUEST["raphaelEnd$i"]) : $end);
	
	$types = explode(',', $types);
	
	$headerlib->add_jsfile("lib/jquery.sheet/plugins/raphael-min.js", "external");
	$headerlib->add_jsfile("lib/jquery.sheet/plugins/g.raphael-min.js", "external");
	$headerlib->add_jq_onready("
		$('.cDashDate').datepicker();
	");
	
	$usersTrackerItems = array();
	foreach($tikilib->fetchAll("
		SELECT itemId FROM tiki_tracker_items WHERE createdBy = ?
	", array("simon")) as $item) {
		$usersTrackerItems[] = $item['itemId'];
	}
	
	foreach($types as $type) {
		if ($type == "trackeritems") {
			$data = array();
			$dates = array();
			
			//simon should be replaced with global $user when done
			foreach(LogsQueryLib::trackerItem()->start($start)->end($end)->countByDateFilterId($usersTrackerItems) as $log) {
				$data[] = $log['count'] * 1;
				$dates[] = $log['date'];
			}

			$headerlib->add_jq_onready("				
				var r = Raphael($('#raphaelTrackeritems$i')[0]);
				
				var data = ".json_encode($data).";
				var dates = ".json_encode($dates).";
				
				r.g.barchart(10,10, $('#raphaelTrackeritems$i').width(),$('#raphaelTrackeritems$i').height(), [data])
					.hover(function () {
						this.flag = r.g.popup(
							this.bar.x,
							this.bar.y,
							dates[this.bar.id] + ' - ' + this.bar.value || '0'
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
				
				r.g.label($('#raphaelTrackeritems$i').width() / 2,30, 'Tracker Item Activity Grouped By Date');
			");
			
			$result .= "<div id='raphaelTrackeritems$i' style='width: 100%; height: 400px; display: block;'></div>";
		}
		
		if ($type == "toptrackeritemsusers") {
			$hits = array();
			$users = array();
			
			//simon should be replaced with global $user when done
			foreach(LogsQueryLib::trackerItem()->start($start)->end($end)->countUsersFilterId($usersTrackerItems) as $key=>$count) {
				$hits[] = $count;
				$users[] = $key;
			}

			$headerlib->add_jq_onready("				
				var r = Raphael($('#raphaelTrackeritemsUsers$i')[0]);
				
				var hits = ".json_encode($hits).";
				var users = ".json_encode($users).";
				
				r.g.barchart(10,10, $('#raphaelTrackeritemsUsers$i').width(),$('#raphaelTrackeritemsUsers$i').height(), [hits])
					.hover(function () {
						this.flag = r.g.popup(
							this.bar.x,
							this.bar.y,
							users[$(this.bar.node).index() - 2] + ' - ' + this.bar.value || '0'
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
				
				r.g.label($('#raphaelTrackeritemsUsers$i').width() / 2,30, 'Tracker Item Activity Grouped By Users');
			");
			
			$result .= "<div id='raphaelTrackeritemsUsers$i' style='width: 100%; height: 400px; display: block;'></div>";
		}
	}
	
	return "
			<style>
				.header {
					font-size: 16px;
				}
				.headerHelper {
					font-size: 12px;
					float: right;
					padding: 0px;
					margin-top: -7px;
				}
			</style>
			<div class='ui-widget ui-widget-content ui-corner-all'>
				<h3 class='header ui-state-default ui-corner-tl ui-corner-tr' style='margin: 0; padding: 5px;'>
					".tr("Contributions Dashboard")."
					<form class='headerHelper'>
						".tr("Date Range")."
						<input type='text' name='raphaelStart$i' id='raphaelStart$i' class='cDashDate' value='".strftime("%m/%d/%Y", $start)."' />
						<input type='text' name='raphaelEnd$i' id='raphaelEnd$i' class='cDashDate' value='".strftime("%m/%d/%Y", $end)."' />
						<input type='hidden' name='refresh' value='1' />
						<input type='submit' id='raphaelUpdate$i' value='".tr("Update")."' />
					</form>
				</h3>
				$result
			</div>";
}
