<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Reports_ManagerTest extends TikiTestCase
{
	protected $obj;
	
	protected $reportsUsers;
	
	protected $reportsCache;
	
	protected function setUp()
	{
		$this->reportsUsers = $this->getMockBuilder('Reports_Users')->disableOriginalConstructor()->getMock();
		
		$this->reportsCache = $this->getMockBuilder('Reports_Cache')->disableOriginalConstructor()->getMock();
		
		$this->obj = new Reports_Manager($this->reportsUsers, $this->reportsCache);
	}
	
	public function testDelete_shouldCallMethodToDeleteUserPreferenceAndMethodToDeleteCache()
	{
		$user = 'test';
		
		$this->reportsUsers->expects($this->once())->method('delete')->with($user);
		$this->reportsCache->expects($this->once())->method('delete')->with($user);
		
		$this->obj->delete($user);
	}
	
	public function testAddToCache_shouldGetUsersUsingPeriodicReportsAndCallMethodToAddToCache()
	{
		$watches = array(
			array('user' => 'admin'),
			array('user' => 'test'),
			array('user' => 'notUsingPeriodicReports')
		);
		
		$data = array('event' => 'wiki_page_changed');
		 
		$users = array('admin', 'test');
		
		$this->reportsUsers->expects($this->once())->method('getAllUsers')
			->will($this->returnValue($users));
			
		$this->reportsCache->expects($this->once())->method('add')->with($watches, $data, $users);
		
		$this->obj->addToCache($watches, $data);
	}
}