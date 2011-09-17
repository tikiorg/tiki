<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
}
