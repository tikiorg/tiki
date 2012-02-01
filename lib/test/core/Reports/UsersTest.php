<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
		
		$this->reportsCache = $this->getMock('Reports_Cache', array('delete'), array($this->db));
		
		$this->dt = new DateTime();
		$this->dt->setTimezone(new DateTimeZone('UTC'));
		$this->dt->setTimestamp('1326734528');
		
		$this->obj = new Reports_Users($this->db, $this->dt, $this->reportsCache);
		
		parent::setUp();
	}
	
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(dirname(__FILE__) . '/fixtures/user_reports_dataset.xml');
	}
	
	public function testDelete_shouldDeleteUserReportsPreferencesAndCacheEntries()
	{
		$user = 'admin';
		
		$expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/user_reports_dataset_delete.xml')
			->getTable('tiki_user_reports');
		
		$this->reportsCache->expects($this->once())->method('delete')->with($user);
		
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
		$obj->expects($this->once())->method('save')->with('test', 'daily', 'detailed', 'html', 1);
		$obj->addUserToDailyReports(array('user' => 'test'));
	}
	
	public function testGetUsers_shouldReturnArrayWithUsers()
	{
		$expectedResult = array('test');
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
}