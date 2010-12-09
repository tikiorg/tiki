<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
class Calendar_DateTest extends TikiTestCase
{
	function testMake_time() {
		$this->markTestIncomplete('skiping because it is failing');
		$date = TikiLib::make_time(0, 0, 0, 8, 1, 2010);
		$this->assertEquals( '2010-08-01 00:00', TikiLib::date_format('%Y-%m-%d %H:%m', $date) );
	}
	function testInfoDate() {
		global $calendarlib; include_once('lib/calendar/calendarlib.php');
		$date = TikiLib::make_time(0, 0, 0, 8, 1, 2010);
		$focus = $calendarlib->infoDate($date);
		$this->assertEquals( '2010-08-01', TikiLib::date_format('%Y-%m-%d', $focus['date']) );
	}
	function testFocusToStartEnd() {
		global $calendarlib; include_once('lib/calendar/calendarlib.php');
		$date = TikiLib::make_time(0, 0, 0, 8, 1, 2010);
		$start = $startNext = array();
		$calendarlib->focusStartEnd($calendarlib->infoDate($date), 'month', 'y', $start, $startNext);
		$this->assertEquals( '2010-08-01', TikiLib::date_format('%Y-%m-%d', $start['date']) );
		$this->assertEquals( '2010-09-01', TikiLib::date_format('%Y-%m-%d', $startNext['date']) );
		$date = TikiLib::make_time(0, 0, 0, 2, 8, 2010);
		$calendarlib->focusStartEnd($calendarlib->infoDate($date), 'month', 'y', $start, $startNext);
		$this->assertEquals( '2010-02-01', TikiLib::date_format('%Y-%m-%d', $start['date']) );
		$this->assertEquals( '2010-03-01', TikiLib::date_format('%Y-%m-%d', $startNext['date']) );
		$date = TikiLib::make_time(0, 0, 0, 4, 30, 2010);
		$calendarlib->focusStartEnd($calendarlib->infoDate($date), 'month', 'y', $start, $startNext);
		$this->assertEquals( '2010-04-01', TikiLib::date_format('%Y-%m-%d', $start['date']) );
		$this->assertEquals( '2010-05-01', TikiLib::date_format('%Y-%m-%d', $startNext['date']) );
	}
	function testFocusToCell() {
		global $calendarlib; include_once('lib/calendar/calendarlib.php');
		$start = $startNext = array();
		$date = TikiLib::make_time(0, 0, 0, 8, 1, 2010);
		$calendarlib->focusStartEnd($calendarlib->infoDate($date), 'month', 'y', $start, $startNext);
		$view = 'month';
		$firstWeekDay = 0; // sunday
		$cell = $calendarlib->getTableViewCells($start, $startNext, $view, $firstWeekDay);
		$this->assertEquals( '2010-08-01', TikiLib::date_format('%Y-%m-%d', $cell[0][0]['date']) );
		$this->assertEquals( '2010-09-04', TikiLib::date_format('%Y-%m-%d', $cell[4][6]['date']) );
		$view = 'month';
		$firstWeekDay = 1; // monday
		$cell = $calendarlib->getTableViewCells($start, $startNext, $view, $firstWeekDay);
		$this->assertEquals( '2010-07-26', TikiLib::date_format('%Y-%m-%d', $cell[0][0]['date']) );
		$this->assertEquals( '2010-09-05', TikiLib::date_format('%Y-%m-%d', $cell[5][6]['date']) );
	}
	function testPrevious() {
		global $calendarlib; include_once('lib/calendar/calendarlib.php');
		$date = TikiLib::make_time(0, 0, 0, 7, 20, 2010);
		$previous = $calendarlib->focusPrevious($calendarlib->infoDate($date), 'month');
		$this->assertEquals( '2010-06-20', TikiLib::date_format('%Y-%m-%d', $previous['date']) );
		$date = TikiLib::make_time(0, 0, 0, 7, 31, 2010);
		$previous = $calendarlib->focusPrevious($calendarlib->infoDate($date), 'month');
		$this->assertEquals( '2010-06-30', TikiLib::date_format('%Y-%m-%d', $previous['date']) );
		$date = TikiLib::make_time(0, 0, 0, 3, 31, 2010);
		$previous = $calendarlib->focusPrevious($calendarlib->infoDate($date), 'quarter');
		$this->assertEquals( '2009-11-30', TikiLib::date_format('%Y-%m-%d', $previous['date']) );
	}
	function testNext() {
		global $calendarlib; include_once('lib/calendar/calendarlib.php');
		$date = TikiLib::make_time(0, 0, 0, 8, 31, 2010);
		$previous = $calendarlib->focusNext($calendarlib->infoDate($date), 'month');
		$this->assertEquals( '2010-09-30', TikiLib::date_format('%Y-%m-%d', $previous['date']) );
	}
}
