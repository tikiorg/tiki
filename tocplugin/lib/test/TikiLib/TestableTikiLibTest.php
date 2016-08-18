<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TestableTikiLibTest extends TikiTestCase
{
	public function testOverrideLib_shouldChangeValueReturnedByLib()
	{
		$obj = new TestableTikiLib;
		
		$this->assertEquals('TikiLib', get_class(TikiLib::lib('tiki')));
		$obj->overrideLibs(array('tiki' => new stdClass));
		$this->assertEquals('stdClass', get_class(TikiLib::lib('tiki')));
	}
	
	public function testOverrideLib_shouldRestoreDefaultValueAfterObjectDestruction()
	{
		$obj = new TestableTikiLib;
		
		$this->assertEquals('TikiLib', get_class(TikiLib::lib('tiki')));
		$obj->overrideLibs(array('tiki' => new stdClass));
		$this->assertEquals('stdClass', get_class(TikiLib::lib('tiki')));
		
		unset($obj);
		$this->assertEquals('TikiLib', get_class(TikiLib::lib('tiki')));
	}
	
	public function testOverrideLib_shouldWorkWithMockObjects()
	{
		$obj = new TestableTikiLib;
		
		$calendarlib = $this->getMock('MockCalendarLib', array('get_item'));
		$calendarlib->expects($this->never())->method('get_item');
		
		$this->assertEquals('CalendarLib', get_class(TikiLib::lib('calendar')));
		$obj->overrideLibs(array('calendar' => $calendarlib));
		$this->assertContains('Mock_MockCalendarLib_', get_class(TikiLib::lib('calendar')));
	}
	
	public function testOverrideLib_checkIfLibReturnedToOriginalStateAfterLastTest()
	{
		$this->assertEquals('CalendarLib', get_class(TikiLib::lib('calendar')));
	}
}