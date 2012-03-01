<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/webmail/tikimaillib.php');

class Reports_SendTest extends TikiTestCase
{
	protected $obj;
	
	protected $dt;
	
	protected function setUp()
	{
		$this->dt = new DateTime;
		$this->dt->setTimestamp('1326909909');
		
		$this->mail = $this->getMock('TikiMail');
		
		$this->obj = new Reports_Send($this->dt, $this->mail);
	}
	
	public function test()
	{
		// under construction
	}
	
}