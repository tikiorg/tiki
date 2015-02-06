<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/test/TikiDatabaseTestCase.php');

class Reports_EndToEndTest extends TikiDatabaseTestCase
{
	protected function setUp()
	{
		$this->markTestSkipped('Strangely enough, this loads two different classes if TikiMail');
		$this->dt = new DateTime;
		$this->dt->setTimestamp(strtotime('2012-03-27 15:55:16'));

		$this->mail = $this->getMock('TikiMail');

		$this->tikilib = $this->getMock('TikiLib', array('get_user_preference'));

		$this->overrideLibs = new TestableTikiLib;
		$this->overrideLibs->overrideLibs(array('calendar' => $this->getMock('MockCalendarLib', array('get_item'))));

		$tikiPrefs = array('short_date_format' => '%Y-%m-%d');

		$this->obj = Reports_Factory::build('Reports_Manager', $this->dt, $this->mail, $this->tikilib, $tikiPrefs);

		parent::setUp();
	}

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(dirname(__FILE__) . '/fixtures/end_to_end_test_dataset.xml');
	}

	public function testReportsEndToEnd_shouldUpdateLastReportFieldInUsersTable()
	{
		$this->obj->send();

		$expectedUserReportsTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/end_to_end_test_result_dataset.xml')
			->getTable('tiki_user_reports');

		$queryUserReportsTable = $this->getConnection()->createQueryTable('tiki_user_reports', 'SELECT * FROM tiki_user_reports');
		$this->assertTablesEqual($expectedUserReportsTable, $queryUserReportsTable);
	}

	public function testReportsEndToEnd_shouldCleanReportsCacheAfterSendingMessages()
	{
		$this->obj->send();

		$expectedCacheTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/end_to_end_test_result_dataset.xml')
			->getTable('tiki_user_reports_cache');
		$queryCacheTable = $this->getConnection()->createQueryTable('tiki_user_reports_cache', 'SELECT * FROM tiki_user_reports_cache');
		$this->assertEquals(0, $queryCacheTable->getRowCount());
	}

	public function testReportsEndToEnd_shouldSendEmail()
	{
		$this->mail->expects($this->once())->method('setUser')->with('test');
		$this->mail->expects($this->once())->method('setHtml')->with(file_get_contents(dirname(__FILE__) . '/fixtures/email_body.txt'));
		$this->mail->expects($this->once())->method('setSubject')->with('Report from 2012-03-27 (20 changes)');
		$this->mail->expects($this->once())->method('buildMessage');
		$this->mail->expects($this->once())->method('send')->with(array('test@test.com'));

		$this->obj->send();
	}
}
