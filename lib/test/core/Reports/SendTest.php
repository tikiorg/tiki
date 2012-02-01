<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Reports_SendTest extends TikiDatabaseTestCase
{
	protected $obj;
	
	protected $dt;
	
	protected function setUp()
	{
		$db = TikiDb::get();
		$this->obj = new Reports_Send($db);
		$this->dt = new DateTime;
		$this->dt->setTimestamp('1326909909');
		
		parent::setUp();
	}
	
	public function getDataSet()
	{
		return $this->createMySQLXMLDataSet(dirname(__FILE__) . '/fixtures/user_reports_dataset.xml');
	}

	public function test()
	{
		
	}
}