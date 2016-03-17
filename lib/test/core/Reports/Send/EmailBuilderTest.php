<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Reports_Send_EmailBuilderTest extends TikiTestCase
{
	protected $obj;
	
	protected $tikilib;
	
	protected function setUp()
	{
		$this->tikilib = $this->getMockBuilder('TikiLib')->getMock();
		$this->factory = $this->getMock('Reports_Send_EmailBuilder_Factory');
		
		$this->obj = new Reports_Send_EmailBuilder($this->tikilib, new Reports_Send_EmailBuilder_Factory);

		$this->defaultReportPreferences = array('type' => 'plain', 'view' => 'detailed');
	}

	public function testMakeEmailBody_shouldReturnStringIfNothingHappened()
	{
		$this->assertEquals('Nothing has happened.', $this->obj->makeEmailBody(array(), $this->defaultReportPreferences));
	}
	
	public function testMakeEmailBody_shouldReturnCalendarChangedReportInDetailedViewMode()
	{
		$this->tikilib->expects($this->exactly(2))->method('get_short_datetime')
			->will($this->returnValue('2011-09-13 11:19'));
		
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
		
		$output = $this->obj->makeEmailBody($reportCache, $this->defaultReportPreferences);

		$this->assertContains('2011-09-13 11:19: admin added or updated event Calendar item name', $output);
	}
	
	public function testMakeEmailBody_shouldReturnTrackerItemCommentReportInDetailedViewMode()
	{
		$this->tikilib->expects($this->once())->method('get_short_datetime')
			->will($this->returnValue('2011-09-12 20:30'));
		
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
		
		$output = $this->obj->makeEmailBody($reportCache, $this->defaultReportPreferences);

		$this->assertContains('2011-09-12 20:30: admin added a new comment to Tracker item name', $output);
	}
	
	public function testMakeEmailBody_shouldUseCategoryChangedObject()
	{
		$obj = new Reports_Send_EmailBuilder($this->tikilib, $this->factory);
		
		$reportCache = array(
			array(
				'user' => 'admin',
				'categoryId' => 1,
    			'event' => 'category_changed',
    			'data' => array('action' => 'object entered category', 'user' => 'admin', 'objectType' => '', 'objectUrl' => '', 'objectName' => '', 'categoryId' => '', 'categoryName' => ''),
    			'time' => '2011-09-12 20:30:31',
			),
		);
		
		$categoryChanged = $this->getMock('Reports_Send_EmailBuilder_CategoryChanged');
		$categoryChanged->expects($this->once())->method('getTitle');
		$categoryChanged->expects($this->once())->method('getOutput');
		
		$this->factory->expects($this->once())->method('build')
			->with('category_changed')->will($this->returnValue($categoryChanged));
			
		$obj->makeEmailBody($reportCache, $this->defaultReportPreferences);
	}
}