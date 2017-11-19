<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @group unit
 *
 */

class Perms_Check_IndirectTest extends TikiTestCase
{
	function testUnknownIndirectionIsFalse()
	{
		$indirect = new Perms_Check_Indirect(['view' => 'admin_wiki',]);

		$mock = $this->createMock('Perms_Resolver');
		$mock->expects($this->never())
			->method('check');

		$this->assertFalse($indirect->check($mock, [], 'edit', ['Admins', 'Anonymous']));
	}

	function testCallForwarded()
	{
		$indirect = new Perms_Check_Indirect(['view' => 'admin_wiki',]);

		$mock = $this->createMock('Perms_Resolver');
		$mock->expects($this->once())
			->method('check')
			->with($this->equalTo('admin_wiki'), $this->equalTo(['Admins', 'Anonymous']))
			->will($this->returnValue(true));

		$this->assertTrue($indirect->check($mock, [], 'view', ['Admins', 'Anonymous']));
	}

	function testCallForwardedWhenFalseToo()
	{
		$indirect = new Perms_Check_Indirect(['view' => 'admin_wiki',]);

		$mock = $this->createMock('Perms_Resolver');
		$mock->expects($this->once())
			->method('check')
			->with($this->equalTo('admin_wiki'), $this->equalTo(['Admins', 'Anonymous']))
			->will($this->returnValue(false));

		$this->assertFalse($indirect->check($mock, [], 'view', ['Admins', 'Anonymous']));
	}
}
