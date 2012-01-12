<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/reportslib.php';

class ReportsLibTest extends TikiTestCase
{
	protected function setUp()
	{	
		$this->defaultReportPreferences = array('type' => 'plain');
		
		$this->obj = new reportsLib;
	}
	
	public function testMakeHtmlEmailBody_shouldReturnStringIfNothingHappened()
	{
		$this->assertEquals('Nothing has happened.', $this->obj->makeHtmlEmailBody(array(), $this->defaultReportPreferences));
	}
	
	public function testMakeHtmlEmailBody_shouldReturnCalendarChangedReportInDetailedViewMode()
	{
		$calendarlib = $this->getMock('MockCalendarLib', array('get_item'));
		$calendarlib->expects($this->exactly(2))
			->method('get_item')
			->will($this->returnValue(array('name' => 'Calendar item name')));

		$tikilib = new TestableTikiLib;
		$tikilib->overrideLibs(array('calendar' => $calendarlib));
		
		$this->defaultReportPreferences['view'] = 'detailed';
		
		$reportCache = array(
			array(
				'user' => 'admin',
    			'event' => 'calendar_changed',
    			'data' => array('event' => 'calendar_changed', 'calitemId' => '2', 'user' => 'admin'),
    			'time' => '2011-09-12 20:30:31',
			),
			array(
				'user' => 'admin',
    			'event' => 'calendar_changed',
    			'data' => array('event' => 'calendar_changed', 'calitemId' => '1', 'user' => 'admin'),
    			'time' => '2011-09-13 11:19:31',
			),
		);
		
		$output = $this->obj->makeHtmlEmailBody($reportCache, $this->defaultReportPreferences);

		$this->assertContains('12.09. 20:30: admin added or updated event Calendar item name', $output);
	}
	
	public function testMakeHtmlEmailBody_shouldReturnTrackerItemCommentReportInDetailedViewMode()
	{
		$trklib = $this->getMock('MockTrackerLib', array('get_tracker', 'get_isMain_value'));
		$trklib->expects($this->once())->method('get_tracker');
		$trklib->expects($this->once())
			->method('get_isMain_value')
			->will($this->returnValue('Tracker item name'));	

		$tikilib = new TestableTikiLib;
		$tikilib->overrideLibs(array('trk' => $trklib));
		
		$this->defaultReportPreferences['view'] = 'detailed';
		
		$reportCache = array(
			array(
				'user' => 'admin',
    			'event' => 'tracker_item_comment',
    			'data' => array('event' => 'tracker_item_comment', 'trackerId' => '2', 'itemId' => '4', 'threadId' => '13', 'user' => 'admin'),
    			'time' => '2011-09-12 20:30:31',
			),
		);
		
		$output = $this->obj->makeHtmlEmailBody($reportCache, $this->defaultReportPreferences);

		$this->assertContains('12.09. 20:30: admin added a new comment to Tracker item name', $output);
	}

}
