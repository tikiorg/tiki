<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiConnectTest extends TikiTestCase
{
	protected function setUp()
	{
		$this->obj = new TikiConnect();
	}
	
	protected function tearDown()
	{
	}
	
	public function testBuildConnectData()
	{

		$this->obj = new TikiConnect();		// for now just check it returns something
		$data = $this->obj->buildConnectData();	// TODO check status etc
		
		$this->assertTrue(count($data) > 0);
	}
	
}