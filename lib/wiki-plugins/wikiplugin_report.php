<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_reports.php 33195 2011-03-02 17:43:40Z changi67 $

function wikiplugin_report_info() {
	return array(
		'name' => tra('Report'),
		'documentation' => 'Report',
		'description' => tra('Build a report, and store it in a wiki page'),
		'prefs' => array( 'wikiplugin_report' ),
		'body' => tra('The wiki syntax report settings'),
		'icon' => 'pics/icons/mime/zip.png',
		'params' => array(
			'view' => array(
				'name' => tra('Report View'),
				'description' => tra('Report Plugin View'),
				'required' => true,
				'default' => 'sheet',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Sheet'), 'value' => 'sheet'), 
					array('text' => tra('Chart'), 'value' => 'chart')
				)
			),
			'name' => array(
				'name' => tra('Report Name'),
				'description' => tra('Report Plugin Name, sometimes used headings and reference'),
				'required' => true,
				'default' => 'Report Type',
			),
		),
	);
}

function wikiplugin_report( $data, $params ) {
	global $tikilib,$headerlib;
	extract ($params,EXTR_SKIP);
	static $report = 0;
	++$report;
	$i = $report;
	
	$report = Report_Builder::loadFromWikiSyntax($data);
	
	$view = (!empty($view) ? $view : 'sheet');
	$name = (!empty($name) ? $name : '');
	
	switch($view) {
		case 'sheet':
			TikiLib::lib("sheet")->setup_jquery_sheet();
			
			$headerlib->add_jq_onready("
			
				$('#reportPlugin$i')
					.sheet({
						editable: false,
						buildSheet: true
					});
				
			");
			
			return "~np~<div id='reportPlugin$i'>" . $report->outputSheet($name) . "</div>~/np~";
			break;
	}
}
