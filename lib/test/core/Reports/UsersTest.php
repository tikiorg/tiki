<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Reports_UsersTest extends TikiDatabaseTestCase
{
	protected $obj;

	protected $db;

	protected $dt;

	protected $reportsCache;

	protected function setUp()
	{
		$this->db = TikiDb::get();

		$this->dt = new DateTime();
		$this->dt->setTimezone(new DateTimeZone('UTC'));
		$this->dt->setTimestamp('1326734528');

		$this->obj = new Reports_Users($this->db, $this->dt);

		parent::setUp();
	}

	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(dirname(__FILE__) . '/fixtures/user_reports_dataset.xml');
	}

	public function testDelete_shouldDeleteUserReportsPreferences()
	{
		$user = 'admin';

		$expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/user_reports_dataset_delete.xml')
			->getTable('tiki_user_reports');

		$this->obj->delete($user);

		$queryTable = $this->getConnection()->createQueryTable('tiki_user_reports', 'SELECT * FROM tiki_user_reports');

		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	public function testGet_shouldReturnEmptyIfUserIsNotUsingReports()
	{
		$this->assertEmpty($this->obj->get('someuserNotUsingReports'));
	}

	public function testGet_shouldReturnUsersReportsPreferences()
	{
		$expectedResult = array('id' => 2, 'interval' => 'daily', 'view' => 'detailed', 'type' => 'html',
			'always_email' => 1, 'last_report' => '2012-01-15 12:22:08');

		$this->assertEquals($expectedResult, $this->obj->get('test'));
	}

	public function testSave_shouldInsertData()
	{
		$expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/user_reports_dataset_insert.xml')
			->getTable('tiki_user_reports');

		$this->obj->save('newUser', 'weekly', 'detailed', 'html', 1);

		$queryTable = $this->getConnection()->createQueryTable('tiki_user_reports', 'SELECT * FROM tiki_user_reports');

		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	public function testSave_shouldUpdateData()
	{
		$expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/user_reports_dataset_update.xml')
			->getTable('tiki_user_reports');

		$this->obj->save('test', 'weekly', 'detailed', 'html', 1);

		$queryTable = $this->getConnection()->createQueryTable('tiki_user_reports', 'SELECT * FROM tiki_user_reports');

		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	public function testAddUserToDailyReport_shouldCallSave()
	{
		$obj = $this->getMock('Reports_Users', array('save'), array(), 'Mock_Reports_Users', false);
		$obj->expects($this->once())->method('save')->with('test', 'daily', 'detailed', 'html', 0);
		$obj->addUserToDailyReports(array('user' => 'test'));
	}

	public function testGetUsersForReport_shouldReturnArrayWithUsers()
	{
		$expectedResult = array('test');
		$users = $this->obj->getUsersForReport();
		$this->assertEquals($expectedResult, $users);
	}

	public function testGetUsersForReport_shouldIncludeNewlyCreatedUsersWithLastReportFieldEmpty()
	{
		$this->db->query(
			"INSERT INTO `tiki_user_reports` (`user`, `interval`, `view`, `type`, `always_email`, `last_report`)
			VALUES ('newUser', 'weekly', 'detailed', 'html', 1, '')"
		);

		$expectedResult = array('test', 'newUser');
		$users = $this->obj->getUsersForReport();
		$this->assertEquals($expectedResult, $users);
	}

	public function testUpdateLastReport_shouldUpdateLastReportField()
	{
		$expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/user_reports_dataset_update_last_report.xml')
			->getTable('tiki_user_reports');

		$this->dt->setTimestamp('1326896528');

		$obj = new Reports_Users($this->db, $this->dt, $this->reportsCache);
		$obj->updateLastReport('test');

		$queryTable = $this->getConnection()->createQueryTable('tiki_user_reports', 'SELECT * FROM tiki_user_reports');

		$this->assertTablesEqual($expectedTable, $queryTable);
	}

	public function testGetAllUsers_shouldReturnAllUsers()
	{
		$users = $this->obj->getAllUsers();
		$this->assertEquals(array('admin', 'test'), $users);
	}
}
