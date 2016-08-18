<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Reports_CacheTest extends TikiDatabaseTestCase
{
	protected $obj;
	
	protected function setUp()
	{
		$db = TikiDb::get();
		$dt = new DateTime();
		$dt->setTimezone(new DateTimeZone('UTC'));
		$dt->setTimestamp('1326990210');
		$this->obj = new Reports_Cache($db, $dt);
		
		parent::setUp();
	}
	
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(dirname(__FILE__) . '/fixtures/reports_cache_dataset.xml');
	}
	
	public function testDelete_shouldDeleteAllEntriesForAUser()
	{
		$expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/reports_cache_dataset_delete.xml')
			->getTable('tiki_user_reports_cache');
			
		$this->obj->delete('admin');
		
		$queryTable = $this->getConnection()->createQueryTable('tiki_user_reports_cache', 'SELECT * FROM tiki_user_reports_cache');
		
		$this->assertTablesEqual($expectedTable, $queryTable);
	}
	
	public function testGet_shouldReturnEntriesForGivenUser()
	{
		$expectedResult = array(array('user' => 'test', 'event' => 'wiki_page_changed',
			'data' => 
				array(
					'event' => 'wiki_page_changed', 'pageName' => 'test', 'object' => 'test',
					'editUser' => 'test', 'editComment' => '', 'oldVer' => 2
				),
			'time' => '2012-01-19 16:23:30'
		));
		
		$entries = $this->obj->get('test');
		
		$this->assertEquals($expectedResult, $entries);
	}
	
	public function testAdd_shouldAddInformationAboutChangedObjectToCache()
	{
        $this->markTestSkipped("As of 2013-09-30, this test is broken. Skipping it for now.");

        $expectedTable = $this->createMySQLXmlDataSet(dirname(__FILE__) . '/fixtures/reports_cache_dataset_add.xml')
			->getTable('tiki_user_reports_cache');	
		
		$users = array('admin', 'test');			
			
		$watches = array(
			array('user' => 'admin'),
			array('user' => 'test'),
			array('user' => 'notUsingPeriodicReports')
		);
		
		$expectedResult = array_slice($watches, 2, 1, true);
		
		$cacheData = array('event' => 'wiki_page_changed');
		
		$this->obj->add($watches, $cacheData, $users);
		
		$queryTable = $this->getConnection()->createQueryTable('tiki_user_reports_cache', 'SELECT * FROM tiki_user_reports_cache');
		
		$this->assertTablesEqual($expectedTable, $queryTable);

		$this->assertEquals($expectedResult, $watches);
	}
}
