<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		$this->dt->setTimestamp(strtotime('2012-03-27 15:55:16'));
		
		$this->mail = $this->getMock('TikiMail', array('send', 'setSubject', 'setHtml', 'setText', 'setUser'));
		$this->builder = $this->getMockBuilder('Reports_Send_EmailBuilder')->disableOriginalConstructor()->getMock();
		
		$tikiPrefs = array('short_date_format' => '%Y-%m-%d');
		
		$this->obj = new Reports_Send($this->dt, $this->mail, $this->builder, $tikiPrefs);
	}
	
	public function testEmailSubject_noChanges()
	{
		$this->mail->expects($this->exactly(2))->method('setSubject')->with('Report from 2012-03-27 (no changes)');
		
		$userData = array('login' => 'test', 'email' => 'test@test.com');
		$reportPreferences = array('type' => 'html');
		
		$this->obj->sendEmail($userData, $reportPreferences, array());
		$this->obj->sendEmail($userData, $reportPreferences, '');
	}
	
	public function testEmailSubject_oneChange()
	{
		$this->mail->expects($this->exactly(1))->method('setSubject')->with('Report from 2012-03-27 (1 change)');
		
		$userData = array('login' => 'test', 'email' => 'test@test.com');
		$reportPreferences = array('type' => 'html');
		
		$this->obj->sendEmail($userData, $reportPreferences, array(1));
	}
	
	public function testEmailSubject_multipleChanges()
	{
		$this->mail->expects($this->exactly(1))->method('setSubject')->with('Report from 2012-03-27 (2 changes)');
		
		$userData = array('login' => 'test', 'email' => 'test@test.com');
		$reportPreferences = array('type' => 'html');
		
		$this->obj->sendEmail($userData, $reportPreferences, array(1, 2));
	}
	
	public function testSendEmail()
	{
		$userData = array('login' => 'test', 'email' => 'test@test.com');
		$reportPreferences = array('type' => 'html');
		$reportCache = array();
		$emailBody = 'body';
		
		$this->builder->expects($this->once())->method('emailBody')
			->with($userData, $reportPreferences, $reportCache)->will($this->returnValue($emailBody));
		$this->mail->expects($this->once())->method('setUser')->with('test');
		$this->mail->expects($this->once())->method('setHtml')->with($emailBody);
		$this->mail->expects($this->once())->method('setSubject')->with('Report from 2012-03-27 (no changes)');
		$this->mail->expects($this->once())->method('send')->with(array('test@test.com'));
		
		$this->obj->sendEmail($userData, $reportPreferences, $reportCache);
	}
}